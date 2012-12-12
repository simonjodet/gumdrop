<?php
/**
 * Generate - Generate web site
 * @package Gumdrop\Commands
 */
namespace Gumdrop\Commands;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

class Generate extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generate the web site')
            ->addArgument('source', InputArgument::OPTIONAL)
            ->addArgument('destination', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $destination = $input->getArgument('destination');

        $Application = new \Gumdrop\Application();

        $Application->setSourceLocation($source);
        $Application->setDestinationLocation($destination);

        $Application->setMarkdownParser(new \dflydev\markdown\MarkdownParser());
        $Application->setTwigEnvironments(new \Gumdrop\TwigEnvironments($Application));
        $Application->setFileHandler(new \Gumdrop\FileHandler($Application));
        $Application->setTwigFileHandler(new \Gumdrop\TwigFileHandler($Application));
        $Application->setEngine(new \Gumdrop\Engine($Application));

        try
        {
            $Application->getEngine()->run();
            $output->writeln('<fg=green>Gumdrop converted your MarkDown files to ' . realpath($Application->getDestinationLocation()) . '</fg=green>');
        }
        catch (\Exception $e)
        {
            $output->writeln('<fg=red>Gumdrop could not generate your site for the following reason:</fg=red>');
            $output->writeln('<fg=black;bg=red>' . $e->getMessage() . '</fg=black;bg=red>');
        }
    }
}
