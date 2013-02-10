#!/usr/bin/env php
<?php
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../autoload.php')) {
    require_once __DIR__ . '/../../autoload.php';
} else {
    echo 'Gumdrop could not find dependencies' . PHP_EOL;
    exit(1);
}

$ConsoleApplication = new \Symfony\Component\Console\Application();
$ConsoleApplication->addCommands(
    array(
        new \Gumdrop\Commands\Generate(),
        new \Gumdrop\Commands\Install(),
        new \Gumdrop\Commands\Reload()
    )
);
$ConsoleApplication->run();
