<?php
namespace Gumdrop\tests\units;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Application.php';
require_once __DIR__ . '/../../Gumdrop/FileHandler.php';
require_once __DIR__ . '/../../Gumdrop/MarkdownFilesHandler.php';

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

        $MarkdownFilesHandler = \Mockery::mock('\Gumdrop\MarkdownFilesHandler');
        $MarkdownFilesHandler
            ->shouldReceive('convertToHtml')
            ->once()->ordered('generate')
            ->with(array('md_file_1.md'), 'destination_path');

        $Application = new \Gumdrop\Application();
        $Application->setFileHandler($FileHandlerMock);
        $Application->setMarkdownFilesHandler($MarkdownFilesHandler);
        $Application->generate('source_path', 'destination_path');
    }
}