<?php
/**
 * Install - Installation command
 * @package Gumdrop\Commands
 */
namespace Gumdrop\Commands;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

class Install extends Command
{
    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Post composer install/update actions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo 'Setting file permissions' . PHP_EOL;
        $root_folder = __DIR__ . '/../../';
        exec('chmod +x ' . $root_folder . 'bin/gumdrop ' . $root_folder . 'bin/autoreload.php ' . $root_folder . 'gumdrop.php');
    }
}
