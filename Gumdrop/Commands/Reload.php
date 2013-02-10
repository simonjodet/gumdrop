<?php
/**
 * Reload - Automatically regenerate the site if source folder changes
 * @package Gumdrop\Commands
 */
namespace Gumdrop\Commands;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

class Reload extends Command
{
    protected function configure()
    {
        $this
            ->setName('reload')
            ->setDescription('Automatically regenerate the site if source folder changes')
            ->addArgument('source', InputArgument::OPTIONAL)
            ->addArgument('destination', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        if (empty($source)) {
            $Application = new \Gumdrop\Application();
            $Application->setEngine(new \Gumdrop\Engine($Application));
            $Application->getEngine()->setSourceFallback();
            $source = $Application->getSourceLocation();
        }
        $destination = $input->getArgument('destination');

        $this->renderSite($source, $destination, $output);
        $last_checksum = $this->sourceChecksum($source);

        while (true) {
            $checksum = $this->sourceChecksum($source);
            if ($last_checksum != $checksum) {
                $this->renderSite($source, $destination, $output);
                $last_checksum = $checksum;
            }
            sleep(2);
        }
    }

    private function renderSite($source, $destination)
    {
        passthru(__DIR__ . '/../../gumdrop.php generate ' . $source . ' ' . $destination);
    }

    private function sourceChecksum($source)
    {
        $Application = new \Gumdrop\Application();
        $Application->setFileHandler(new \Gumdrop\FileHandler($Application));

        $Application->setSourceLocation($source);
        return $Application->getFileHandler()->getSourceFolderHash();
    }


}