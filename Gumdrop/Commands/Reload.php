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
        $destination = $input->getArgument('destination');

        $this->renderSite($source, $destination, $output);
        $last_checksum = $this->sourceChecksum($source);

        while (true)
        {
            $checksum = $this->sourceChecksum($source);
            if ($last_checksum != $checksum)
            {
                $this->renderSite($source, $destination, $output);
                $last_checksum = $checksum;
            }
            sleep(2);
        }
    }

    private function renderSite($source, $destination, OutputInterface $output)
    {
        $command = $this->getApplication()->find('generate');

        $arguments = array(
            'command' => 'generate',
            'source' => $source,
            'destination' => $destination
        );

        $input = new \Symfony\Component\Console\Input\ArrayInput($arguments);
        $command->run($input, $output);
    }

    private function sourceChecksum($source)
    {
        $Application = new \Gumdrop\Application();
        $Application->setFileHandler(new \Gumdrop\FileHandler($Application));

        $Application->setSourceLocation($source);
        return $Application->getFileHandler()->getSourceFolderHash();
    }


}

//renderSite($source, $destination);
//$last_checksum = getChecksum($source);
//
//while (true)
//{
//    $checksum = getChecksum($source);
//    if ($last_checksum != $checksum)
//    {
//        renderSite($source, $destination);
//        $last_checksum = $checksum;
//    }
//    sleep(2);
//}
//exit(0);
//
//function renderSite($source, $destination)
//{
//    passthru(__DIR__ . '/../gumdrop.php generate ' . $source . ' ' . $destination);
//}
//
//function getChecksum($source)
//{
//    exec(__DIR__ . '/../gumdrop.php checksum ' . $source, $output);
//    return $output[0];
//}