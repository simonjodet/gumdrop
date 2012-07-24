<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Engine.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/IMarkdownParser.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/MarkdownParser.php';

class Engine extends \Gumdrop\Tests\TestCase
{
    public function testConvertMarkdownToHtmlCallsThePageMethod()
    {
        $Page1 = \Mockery::mock('\Gumdrop\Page');
        $Page1
            ->shouldReceive('convertMarkdownToHtml')
            ->once();
        $Page2 = \Mockery::mock('\Gumdrop\Page');
        $Page2
            ->shouldReceive('convertMarkdownToHtml')
            ->once();
        $PageCollection = new \Gumdrop\PageCollection(array(
            $Page1,
            $Page2
        ));

        $MarkdownFiles = new \Gumdrop\Engine($this->getApp());
        $MarkdownFiles->convertMarkdownToHtml($PageCollection);
    }

    public function testApplyTwigLayoutCallsThePageMethod()
    {
        $Page1 = \Mockery::mock('\Gumdrop\Page');
        $Page1
            ->shouldReceive('applyTwigLayout')
            ->once();
        $Page2 = \Mockery::mock('\Gumdrop\Page');
        $Page2
            ->shouldReceive('applyTwigLayout')
            ->once();
        $PageCollection = new \Gumdrop\PageCollection(array(
            $Page1,
            $Page2
        ));

        $MarkdownFiles = new \Gumdrop\Engine($this->getApp());
        $MarkdownFiles->applyTwigLayout($PageCollection);
    }

    public function testWriteHtmlFilesWritePagesToHtmFiles()
    {
        $Page1 = \Mockery::mock('\Gumdrop\Page');
        $Page1
            ->shouldReceive('writeHtmFiles')
            ->with('destination')
            ->once();
        $Page2 = \Mockery::mock('\Gumdrop\Page');
        $Page2
            ->shouldReceive('writeHtmFiles')
            ->with('destination')
            ->once();
        $PageCollection = new \Gumdrop\PageCollection(array(
            $Page1,
            $Page2
        ));

        $MarkdownFiles = new \Gumdrop\Engine($this->getApp());
        $MarkdownFiles->writeHtmFiles($PageCollection, 'destination');
    }
}