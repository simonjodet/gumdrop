<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Application.php';
require_once __DIR__ . '/../../Gumdrop/FileHandler.php';
require_once __DIR__ . '/../../Gumdrop/Engine.php';

class Application extends \Gumdrop\Tests\TestCase
{
    public function testGenerateListsFilesThenConvertThem()
    {
        $Page = new \Gumdrop\Page($this->getApp());
        $PageCollection = new \Gumdrop\PageCollection(array($Page));

        $FileHandlerMock = \Mockery::mock('\Gumdrop\FileHandler');
        $FileHandlerMock
            ->shouldReceive('listMarkdownFiles')
            ->ordered('generate')
            ->andReturn($PageCollection);

        $FileHandlerMock
            ->shouldReceive('getMarkdownFiles')
            ->ordered('generate')
            ->with($PageCollection)
            ->andReturnUsing(
            function() use($PageCollection)
            {
                $PageCollection[0]->setMarkdownContent('md content');
                return $PageCollection;
            });

        $PageCollection[0]->setMarkdownContent('md content');


        $Engine = \Mockery::mock('\Gumdrop\Engine');
        $Engine
            ->shouldReceive('run')
            ->once()
            ->with($PageCollection);

        $Application = new \Gumdrop\Application();
        $Application->setFileHandler($FileHandlerMock);
        $Application->setEngine($Engine);
        $Application->setDestinationLocation('destination_path');
        $Application->generate();
    }
}