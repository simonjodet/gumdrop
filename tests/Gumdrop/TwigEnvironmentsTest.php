<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/TwigEnvironments.php';
require_once __DIR__ . '/../../vendor/simonjodet/twig/lib/Twig/Autoloader.php';

class TwigEnvironments extends \Gumdrop\Tests\TestCase
{
    public static function setUpBeforeClass()
    {
        \Twig_Autoloader::register();
    }

    public function testGetLayoutEnvironmentReturnsTheExpectedEnvironment()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'testFile2.md',
                    'content' => ''
                ),
                array(
                    'path' => '_layout/default.twig',
                    'content' => ''
                ), array(
                    'path' => '_layout/page.twig',
                    'content' => ''
                )
            )
        ));

        $app = $this->getApp();
        $app->setSourceLocation($FSTestHelper->getTemporaryPath());

        $Twig = new \Gumdrop\TwigEnvironments($app);
        $LayoutEnvironment = $Twig->getLayoutEnvironment();
        $Loader = $LayoutEnvironment->getLoader();
        $paths = $Loader->getPaths();

        $this->assertInstanceOf('\Twig_Environment', $LayoutEnvironment);
        $this->assertInstanceOf('\Twig_Loader_Filesystem', $Loader);
        $this->assertEquals($FSTestHelper->getTemporaryPath() . '/_layout', $paths[0]);
    }

    public function testGetLayoutEnvironmentReturnsNullWhenLayoutFolderDoesNotExist()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'testFile2.md',
                    'content' => ''
                )
            )
        ));

        $app = $this->getApp();
        $app->setSourceLocation($FSTestHelper->getTemporaryPath());

        $Twig = new \Gumdrop\TwigEnvironments($app);
        $this->assertNull($Twig->getLayoutEnvironment());
    }

    public function testGetPageEnvironmentReturnsTheExpectedEnvironment()
    {
        $app = $this->getApp();

        $Twig = new \Gumdrop\TwigEnvironments($app);
        $LayoutEnvironment = $Twig->getPageEnvironment();
        $Loader = $LayoutEnvironment->getLoader();

        $this->assertInstanceOf('\Twig_Environment', $LayoutEnvironment);
        $this->assertInstanceOf('\Twig_Loader_String', $Loader);
    }

    public function testGetSiteEnvironmentReturnsTheExpectedEnvironment()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'testFile2.md',
                    'content' => ''
                ),
                array(
                    'path' => '_layout/default.twig',
                    'content' => ''
                ), array(
                    'path' => '_layout/page.twig',
                    'content' => ''
                )
            )
        ));

        $app = $this->getApp();
        $app->setSourceLocation($FSTestHelper->getTemporaryPath());

        $Twig = new \Gumdrop\TwigEnvironments($app);
        $LayoutEnvironment = $Twig->getSiteEnvironment();
        $Loader = $LayoutEnvironment->getLoader();
        $paths = $Loader->getPaths();

        $this->assertInstanceOf('\Twig_Environment', $LayoutEnvironment);
        $this->assertInstanceOf('\Twig_Loader_Filesystem', $Loader);
        $this->assertEquals($FSTestHelper->getTemporaryPath(), $paths[0]);

    }
}