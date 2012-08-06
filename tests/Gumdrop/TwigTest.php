<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Twig.php';
require_once __DIR__ . '/../../vendor/twig/twig/lib/Twig/Autoloader.php';

class Twig extends \Gumdrop\Tests\TestCase
{
    public static function setUpBeforeClass()
    {
        \Twig_Autoloader::register();
    }

    public function testGetLayoutEnvironmentReturnsTheExpectedEnvironment()
    {
        $app = $this->getApp();
        $app->setSourceLocation(__DIR__ . '/markdownFiles/');

        $Twig = new \Gumdrop\Twig($app);
        $LayoutEnvironment = $Twig->getLayoutEnvironment();
        $Loader = $LayoutEnvironment->getLoader();
        $paths = $Loader->getPaths();

        $this->assertInstanceOf('\Twig_Environment', $LayoutEnvironment);
        $this->assertInstanceOf('\Twig_Loader_Filesystem', $Loader);
        $this->assertEquals(__DIR__ . '/markdownFiles//_layout', $paths[0]);
    }
}