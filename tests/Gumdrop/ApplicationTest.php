<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Application.php';
require_once __DIR__ . '/../../Gumdrop/FileHandler.php';
require_once __DIR__ . '/../../Gumdrop/Engine.php';

class Application extends \Gumdrop\Tests\TestCase
{
    public function testGenerateListFilesThenConvertThem()
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
            ->shouldReceive('convertMarkdownToHtml')
            ->once()->ordered('generate')
            ->with($PageCollection)
            ->andReturnUsing(
            function() use($PageCollection)
            {
                $PageCollection[0]->setHtmlContent('html content');
                return $PageCollection;
            });

        $PageCollection[0]->setHtmlContent('html content');


        $Engine
            ->shouldReceive('applyTwigLayout')
            ->once()->ordered('generate')
            ->with($PageCollection)
            ->andReturnUsing(
            function() use($PageCollection)
            {
                $PageCollection[0]->setHtmlContent('twig content');
                return $PageCollection;
            });

        $PageCollection[0]->setHtmlContent('twig content');

        $Engine
            ->shouldReceive('writeHtmFiles')
            ->once()->ordered('generate')
            ->with($PageCollection, 'destination_path');

        $Application = new \Gumdrop\Application();
        $Application->setFileHandler($FileHandlerMock);
        $Application->setEngine($Engine);
        $Application->generate('destination_path');
    }
}