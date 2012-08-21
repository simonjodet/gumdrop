<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/TwigFileHandler.php';

class TwigFiles extends \Gumdrop\Tests\TestCase
{
    public function testListTwigFilesReturnsTwigFiles()
    {
        $FSTestHelper = $this->createTestFSForStaticAndHtmlFiles();
        $location = $FSTestHelper->getTemporaryPath();

        $app = $this->getApp();
        $app->setSourceLocation($location);

        $TwigFileHandler = new \Gumdrop\TwigFileHandler($app);
        $twigFiles = $TwigFileHandler->listTwigFiles();
        $this->assertTrue(in_array('index.twig', $twigFiles));
        $this->assertTrue(in_array('folder/index.twig', $twigFiles));

    }

    public function testListTwigFilesIgnoresTheLayoutFolder()
    {
        $FSTestHelper = $this->createTestFSForStaticAndHtmlFiles();
        $location = $FSTestHelper->getTemporaryPath();

        $app = $this->getApp();
        $app->setSourceLocation($location);

        $TwigFileHandler = new \Gumdrop\TwigFileHandler($app);
        $twigFiles = $TwigFileHandler->listTwigFiles();
        $this->assertFalse(in_array('_layout/file1.twig', $twigFiles));
    }

    public function testRenderTwigFilesRendersTheTwigFiles()
    {
        $FSTestHelper = $this->createTestFSForStaticAndHtmlFiles();
        $location = $FSTestHelper->getTemporaryPath();

        $FSTestHelperForDestination = new \FSTestHelper\FSTestHelper();
        $destination = realpath($FSTestHelperForDestination->getTemporaryPath());

        $PageCollectionMock = \Mockery::mock('\Gumdrop\PageCollection');
        $PageCollectionMock
            ->shouldReceive('exportForTwig')
            ->andReturn(array('some array'));


        $twigFiles = array(
            'index.twig',
            'folder/index.twig'
        );

        $app = $this->getApp();
        $app->setSourceLocation($location);
        $SiteTwigMock = \Mockery::mock('\Twig_Environment');
        $SiteTwigMock
            ->shouldReceive('render')
            ->once()
            ->with('index.twig', array('some array'))
            ->andReturn('index_twig_rendering');

        $SiteTwigMock
            ->shouldReceive('render')
            ->once()
            ->with('folder/index.twig', array('some array'))
            ->andReturn('folder_index_twig_rendering');


        $TwigMock = \Mockery::mock('\Gumdrop\TwigEnvironments');
        $TwigMock
            ->shouldReceive('getSiteEnvironment')
            ->once()
            ->andReturn($SiteTwigMock);

        $app->setTwig($TwigMock);

        $app->setPageCollection($PageCollectionMock);

        $app->setDestinationLocation($destination);

        $TwigFileHandler = new \Gumdrop\TwigFileHandler($app);
        $TwigFileHandler->renderTwigFiles();

        $this->assertStringEqualsFile($destination . '/index.htm', 'index_twig_rendering');
        $this->assertStringEqualsFile($destination . '/folder/index.htm', 'folder_index_twig_rendering');

    }
}