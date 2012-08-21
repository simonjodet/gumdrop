#!/usr/bin/php
<?php
$source = realpath($_SERVER['argv'][1]) . '/';
$destination = realpath($_SERVER['argv'][2]) . '/';
if ($source == '/' || $source === false)
{
    echo 'Given source path is not valid!' . PHP_EOL;
    exit(1);
}
if ($destination == '/' || $destination === false)
{
    echo 'Given destination path is not valid!' . PHP_EOL;
    exit(2);
}

require_once __DIR__ . '/vendor/autoload.php';

$Application = new \Gumdrop\Application();

$Application->setSourceLocation($source);
$Application->setDestinationLocation($destination);

$Application->setMarkdownParser(new \dflydev\markdown\MarkdownParser());
$Application->setTwig(new \Gumdrop\TwigEnvironments($Application));
$Application->setFileHandler(new \Gumdrop\FileHandler($Application));
$Application->setEngine(new \Gumdrop\Engine($Application));

$Application->getEngine()->run();
echo 'Gumdrop converted your MarkDown files converted to ' . $destination . PHP_EOL;
exit(0);