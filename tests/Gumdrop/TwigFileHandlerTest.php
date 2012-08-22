<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/TwigFileHandler.php';

class TwigFileHandler extends \Gumdrop\Tests\TestCase
{
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

        $app = $this->getApp();
        $app->setSourceLocation($location);
        $app->setSiteConfiguration(array('some_configuration'));
        $SiteTwigMock = \Mockery::mock('\Twig_Environment');
        $SiteTwigMock
            ->shouldReceive('render')
            ->once()
            ->with('index.twig', array(
            'site' => array('some_configuration'),
            'pages' => array('some array')
        ))
            ->andReturn('index_twig_rendering');

        $SiteTwigMock
            ->shouldReceive('render')
            ->once()
            ->with('folder/index.twig', array(
            'site' => array('some_configuration'),
            'pages' => array('some array')
        ))
            ->andReturn('folder_index_twig_rendering');


        $TwigMock = \Mockery::mock('\Gumdrop\TwigEnvironments');
        $TwigMock
            ->shouldReceive('getSiteEnvironment')
            ->once()
            ->andReturn($SiteTwigMock);

        $app->setTwigEnvironments($TwigMock);

        $app->setPageCollection($PageCollectionMock);

        $app->setDestinationLocation($destination);

        $twigFiles = array(
            'index.twig',
            'folder/index.twig'
        );
        $FileHandlerMock = \Mockery::mock('\Gumdrop\FileHandler');
        $FileHandlerMock
            ->shouldReceive('listTwigFiles')
            ->once()
            ->andReturn($twigFiles);

        $app->setFileHandler($FileHandlerMock);

        $TwigFileHandler = new \Gumdrop\TwigFileHandler($app);
        $TwigFileHandler->renderTwigFiles();

        $this->assertStringEqualsFile($destination . '/index.htm', 'index_twig_rendering');
        $this->assertStringEqualsFile($destination . '/folder/index.htm', 'folder_index_twig_rendering');

    }
}