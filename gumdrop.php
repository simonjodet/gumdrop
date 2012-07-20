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

$Application->setTwigLoaderFileSystem(new Twig_Loader_Filesystem($source . '/_layout/'));
$Application->setTwigEnvironment(
    new Twig_Environment(
        $Application->getTwigLoaderFileSystem(),
        array(
            'autoescape' => false,
            'strict_variables' => false
        )
    )
);

$Application->setFileHandler(new \Gumdrop\FileHandler($source));
$Application->setEngine(new \Gumdrop\Engine($Application));

$Application->generate($destination);
echo 'Gumdrop converted your MarkDown files converted to ' . $destination . PHP_EOL;