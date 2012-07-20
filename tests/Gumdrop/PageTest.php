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

    public function testApplyTwigLayoutAppliesTheLayoutToPages()
    {
        $app = new \Gumdrop\Application();
        $Twig_Environment = \Mockery::mock('\Twig_Environment');
        $Twig_Environment
            ->shouldReceive('render')
            ->with(
            'page.twig',
            array(
                'content' => 'html content 1'
            ))
            ->andReturn('twig content 1');

        $app->setTwigEnvironment($Twig_Environment);

        $FileHandler = \Mockery::mock('\Gumdrop\FileHandler');
        $FileHandler
            ->shouldReceive('findPageTwigFile')
            ->andReturn(true);

        $app->setFileHandler($FileHandler);

        $Page = new \Gumdrop\Page($app);
        $Page->setHtmlContent('html content 1');

        $Page->applyTwigLayout();

        $this->string($Page->getHtmlContent())->isEqualTo('twig content 1');
    }

    public function testApplyTwigLayoutDoesNotApplyTheLayoutToPagesIfItDoesNotExist()
    {
        $app = new \Gumdrop\Application();
        $FileHandler = \Mockery::mock('\Gumdrop\FileHandler');
        $FileHandler
            ->shouldReceive('findPageTwigFile')
            ->andReturn(false);

        $app->setFileHandler($FileHandler);

        $Page = new \Gumdrop\Page($app);
        $Page->setHtmlContent('html content 1');

        $Page->applyTwigLayout();

        $this->string($Page->getHtmlContent())->isEqualTo('html content 1');
    }
}