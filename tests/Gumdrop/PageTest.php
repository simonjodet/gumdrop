<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Page.php';
require_once __DIR__ . '/../../Gumdrop/PageConfiguration.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/IMarkdownParser.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/MarkdownParser.php';

class Page extends \Gumdrop\Tests\TestCase
{
    public function testSetConfigurationCallsConfigurationExtractHeaderMethod()
    {
        $app = new \Gumdrop\Application();

        $PageConfigurationMock = \Mockery::mock('\Gumdrop\PageConfiguration');
        $PageConfigurationMock
            ->shouldReceive('extractHeader')
            ->with('Markdown content')
            ->once();

        $Page = new \Gumdrop\Page($app);
        $Page->setMarkdownContent('Markdown content');
        $Page->setConfiguration($PageConfigurationMock);
    }

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

        $this->assertEquals($Page->getHtmlContent(), 'html content 1');
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

        $this->assertEquals($Page->getHtmlContent(), 'twig content 1');
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

        $this->assertEquals($Page->getHtmlContent(), 'html content 1');
    }

    public function testApplyTwigLayoutPreferablyUsesLayoutSetInPageConfiguration()
    {
        $app = new \Gumdrop\Application();

        $Twig_Environment = \Mockery::mock('\Twig_Environment');
        $Twig_Environment
            ->shouldReceive('render')
            ->with(
            'twig_layout.twig',
            array(
                'content' => 'html content 1'
            ))
            ->andReturn('twig content 1');

        $app->setTwigEnvironment($Twig_Environment);

        $Page = new \Gumdrop\Page($app);

        $PageConfiguration = new \Gumdrop\PageConfiguration();
        $PageConfiguration->layout = 'twig_layout.twig';

        $Page->setHtmlContent('html content 1');
        $Page->setConfiguration($PageConfiguration);

        $Page->applyTwigLayout();
    }

    public function testWriteHtmlFilesWritePagesToHtmFiles()
    {
        $app = new \Gumdrop\Application();

        $Page = new \Gumdrop\Page($app);
        $Page->setLocation('folder/file_1_path.md');
        $Page->setHtmlContent('twig content 1');

        $destination = TMP_FOLDER . $this->getUniqueId();
        mkdir($destination);

        $Page->writeHtmFiles($destination);

        $this->assertStringEqualsFile($destination . '/folder/file_1_path.htm', 'twig content 1');

        unlink($destination . '/folder/file_1_path.htm');
        rmdir($destination . '/folder');
        rmdir($destination);
    }

}