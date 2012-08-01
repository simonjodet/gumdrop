<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Engine.php';
require_once __DIR__ . '/../../Gumdrop/Configuration.php';
require_once __DIR__ . '/../../Gumdrop/PageConfiguration.php';
require_once __DIR__ . '/../../Gumdrop/PageCollection.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/IMarkdownParser.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/MarkdownParser.php';

class Engine extends \Gumdrop\Tests\TestCase
{
    public function testRunBehavesAsExpected()
    {
        $Page1 = \Mockery::mock('\Gumdrop\Page');
        $Page1
            ->shouldReceive('setConfiguration')
            ->with(\Mockery::type('\Gumdrop\PageConfiguration'))
            ->ordered('page1')
            ->once();
        $Page1
            ->shouldReceive('convertMarkdownToHtml')
            ->ordered('page1')
            ->once();
        $Page1
            ->shouldReceive('applyTwigLayout')
            ->ordered('page1')
            ->once();
        $Page1
            ->shouldReceive('writeHtmFiles')
            ->with('destination')
            ->ordered('page1')
            ->once();
        $Page2 = \Mockery::mock('\Gumdrop\Page');
        $Page2
            ->shouldReceive('setConfiguration')
            ->with(\Mockery::type('\Gumdrop\PageConfiguration'))
            ->ordered('page2')
            ->once();
        $Page2
            ->shouldReceive('convertMarkdownToHtml')
            ->ordered('page2')
            ->once();
        $Page2
            ->shouldReceive('applyTwigLayout')
            ->ordered('page2')
            ->once();
        $Page2
            ->shouldReceive('writeHtmFiles')
            ->ordered('page2')
            ->with('destination')
            ->once();
        $PageCollection = new \Gumdrop\PageCollection(array(
            $Page1,
            $Page2
        ));

        $Engine = new \Gumdrop\Engine($this->getApp());
        $Engine->run($PageCollection, 'destination');
    }
}