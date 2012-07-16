<?php
namespace Gumdrop\tests\units;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Application.php';
require_once __DIR__ . '/../../Gumdrop/FileHandler.php';
require_once __DIR__ . '/../../Gumdrop/Engine.php';

class Application extends \tests\units\TestCase
{
    /**
     * @isNotVoid
     */
    public function testGenerateListFilesThenConvertThem()
    {
        $FileHandlerMock = \Mockery::mock('\Gumdrop\FileHandler');
        $FileHandlerMock
            ->shouldReceive('listMarkdownFiles')
            ->once()->ordered('generate')
            ->with('source_path')
            ->andReturn(array('md_file_1.md'));

        $FileHandlerMock
            ->shouldReceive('getMarkdownFiles')
            ->once()->ordered('generate')
            ->with(array('md_file_1.md'), 'source_path')
            ->andReturn(array('md_file_1.md' => 'md content 1'));

        $Engine = \Mockery::mock('\Gumdrop\Engine');
        $Engine
            ->shouldReceive('convertMarkdownToHtml')
            ->once()->ordered('generate')
            ->with(array('md_file_1.md' => 'md content 1'))
            ->andReturn(array('md_file_1.md' => 'html content 1'));

        $Engine
            ->shouldReceive('applyTwigLayout')
            ->once()->ordered('generate')
            ->with(array('md_file_1.md' => 'html content 1'))
            ->andReturn(array('md_file_1.md' => 'twig content 1'));

        $Engine
            ->shouldReceive('writeHtmFiles')
            ->once()->ordered('generate')
            ->with(array('md_file_1.md' => 'twig content 1'), 'destination_path');

        $Application = new \Gumdrop\Application();
        $Application->setFileHandler($FileHandlerMock);
        $Application->setEngine($Engine);
        $Application->generate('source_path', 'destination_path');
    }
}