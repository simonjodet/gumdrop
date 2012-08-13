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

    public function testGetLayoutEnvironmentReturnsNullWhenLayoutFolderDoesNotExist()
    {
        $app = $this->getApp();
        $app->setSourceLocation(__DIR__ . '/site_without_layout/');

        $Twig = new \Gumdrop\Twig($app);
        $this->assertNull($Twig->getLayoutEnvironment());
    }

    public function testGetPageEnvironmentReturnsTheExpectedEnvironment()
    {
        $app = $this->getApp();
        $app->setSourceLocation(__DIR__ . '/markdownFiles/');

        $Twig = new \Gumdrop\Twig($app);
        $LayoutEnvironment = $Twig->getPageEnvironment();
        $Loader = $LayoutEnvironment->getLoader();

        $this->assertInstanceOf('\Twig_Environment', $LayoutEnvironment);
        $this->assertInstanceOf('\Twig_Loader_String', $Loader);
    }

}