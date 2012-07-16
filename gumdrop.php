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
    exit(1);
}

require_once __DIR__ . '/vendor/autoload.php';
$Application = new \Gumdrop\Application();
$Application->setMarkdownParser(new \dflydev\markdown\MarkdownParser());
$Application->setFileHandler(new \Gumdrop\FileHandler());
$Application->setMarkdownFilesHandler(new \Gumdrop\MarkdownFilesHandler($Application));
$Application->generate($source, $destination);
echo 'Gumdrop converted your MarkDown files converted to ' . $destination . PHP_EOL;