<?php
namespace Gumdrop\tests\units;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Page.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/IMarkdownParser.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/MarkdownParser.php';

class Page extends \tests\units\TestCase
{
    public function testConvertMarkdownToHtmlUsesTheMarkdownParser()
    {
        $app = new \Gumdrop\Application();
        $MarkdownParserMock = \Mockery::mock('\dflydev\markdown\MarkdownParser');
        $MarkdownParserMock
            ->shouldReceive('transformMarkdown')
            ->with('md content 1')
            ->andReturn('html content 1');

        $app->setMarkdownParser($MarkdownParserMock);

        $Page = new \Gumdrop\Page($app);
        $Page->setMarkdownContent('md content 1');

        $Page->convertMarkdownToHtml();

        $this->string($Page->getHtmlContent())->isEqualTo('html content 1');
    }
}