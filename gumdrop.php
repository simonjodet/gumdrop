#!/usr/bin/env php
<?php
$source = realpath($_SERVER['argv'][1]) . '/';
$destination = realpath($_SERVER['argv'][2]) . '/';
if ($source == '/' || $source == realpath(__DIR__) . '/' || $source === false)
{
    echo 'Given source path is not valid!' . PHP_EOL;
    exit(1);
}
if ($destination == '/' || $destination == realpath(__DIR__) . '/' || $destination === false)
{
    $destination = '';
}
if (file_exists(__DIR__ . '/vendor/autoload.php'))
{
    require_once __DIR__ . '/vendor/autoload.php';
}
elseif (file_exists(__DIR__ . '/../../autoload.php'))
{
    require_once __DIR__ . '/../../autoload.php';
}
else
{
    echo 'Gumdrop could not find dependencies' . PHP_EOL;
    exit(1);
}

$Application = new \Gumdrop\Application();

$Application->setSourceLocation($source);
$Application->setDestinationLocation($destination);

$Application->setMarkdownParser(new \dflydev\markdown\MarkdownParser());
$Application->setTwigEnvironments(new \Gumdrop\TwigEnvironments($Application));
$Application->setFileHandler(new \Gumdrop\FileHandler($Application));
$Application->setTwigFileHandler(new \Gumdrop\TwigFileHandler($Application));
$Application->setEngine(new \Gumdrop\Engine($Application));

if (isset($_SERVER['argv'][3]) && $_SERVER['argv'][3] == '-c')
{
    echo $Application->getFileHandler()->getSourceFolderHash() . PHP_EOL;
}
else
{
    try
    {
        $Application->getEngine()->run();
        echo 'Gumdrop converted your MarkDown files to ' . $destination . PHP_EOL;
    }
    catch (\Exception $e)
    {
        echo 'Gumdrop could not generate your site for the following reason: "' . $e->getMessage() . '"' . PHP_EOL;
    }
}
exit(0);