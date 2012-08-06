<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Page.php';
require_once __DIR__ . '/../../Gumdrop/PageConfiguration.php';
require_once __DIR__ . '/../../vendor/twig/twig/lib/Twig/Environment.php';
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
            ->once()
            ->andReturn('Configuration-stripped Markdown content');

        $Page = new \Gumdrop\Page($app);
        $Page->setMarkdownContent('Markdown content');
        $Page->setConfiguration($PageConfigurationMock);

        $this->assertEquals('Configuration-stripped Markdown content', $Page->getMarkdownContent());
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

    public function testRenderLayoutTwigEnvironmentAppliesTheLayoutToPages()
    {
        $app = new \Gumdrop\Application();

        $PageCollection = new \Gumdrop\PageCollection($app);

        $PageConfiguration = new \Gumdrop\PageConfiguration();

        $FileHandler = \Mockery::mock('\Gumdrop\FileHandler');
        $FileHandler
            ->shouldReceive('findPageTwigFile')
            ->andReturn(true);

        $LayoutTwigEnvironment = \Mockery::mock('\Twig_Environment[render]');
        $LayoutTwigEnvironment
            ->shouldReceive('render')
            ->with(
            'page.twig',
            array(
                'content' => 'html content 1',
                'page' => $PageConfiguration,
                'pages' => $PageCollection
            ))
            ->andReturn('twig content 1');

        $app->setFileHandler($FileHandler);

        $Page = new \Gumdrop\Page($app);
        $Page->setConfiguration($PageConfiguration);
        $Page->setHtmlContent('html content 1');
        $Page->setLayoutTwigEnvironment($LayoutTwigEnvironment);
        $Page->setCollection($PageCollection);

        $Page->renderLayoutTwigEnvironment();

        $this->assertEquals($Page->getHtmlContent(), 'twig content 1');
    }

    public function testRenderLayoutTwigEnvironmentDoesNotApplyTheLayoutToPagesIfItDoesNotExist()
    {
        $app = new \Gumdrop\Application();
        $FileHandler = \Mockery::mock('\Gumdrop\FileHandler');
        $FileHandler
            ->shouldReceive('findPageTwigFile')
            ->andReturn(false);

        $app->setFileHandler($FileHandler);

        $Page = new \Gumdrop\Page($app);
        $Page->setHtmlContent('html content 1');

        $Page->renderLayoutTwigEnvironment();

        $this->assertEquals($Page->getHtmlContent(), 'html content 1');
    }

    public function testRenderLayoutTwigEnvironmentPreferablyUsesLayoutSetInPageConfiguration()
    {
        $app = new \Gumdrop\Application();

        $PageCollection = new \Gumdrop\PageCollection($app);

        $PageConfiguration = new \Gumdrop\PageConfiguration();
        $PageConfiguration['layout'] = 'twig_layout.twig';

        $LayoutTwigEnvironment = \Mockery::mock('\Twig_Environment[render]');
        $LayoutTwigEnvironment
            ->shouldReceive('render')
            ->with(
            'twig_layout.twig',
            array(
                'content' => 'html content 1',
                'page' => $PageConfiguration,
                'pages' => $PageCollection
            ))
            ->andReturn('twig content 1');

        $Page = new \Gumdrop\Page($app);

        $Page->setConfiguration($PageConfiguration);
        $Page->setHtmlContent('html content 1');
        $Page->setLayoutTwigEnvironment($LayoutTwigEnvironment);
        $Page->setCollection($PageCollection);

        $Page->renderLayoutTwigEnvironment();
    }

    public function testRenderPageTwigEnvironmentSetsItsResultsToPageHtmlContent()
    {
        $app = new \Gumdrop\Application();

        $PageCollection = new \Gumdrop\PageCollection($app);
        $PageConfiguration = new \Gumdrop\PageConfiguration();

        $PageTwigEnvironment = \Mockery::mock('\Twig_Environment[render]');
        $PageTwigEnvironment
            ->shouldReceive('render')
            ->with(
            'initial html content',
            array(
                'content' => 'initial html content',
                'page' => $PageConfiguration,
                'pages' => $PageCollection
            ))
            ->andReturn('new html content');

        $Page = new \Gumdrop\Page($app);
        $Page->setConfiguration($PageConfiguration);
        $Page->setHtmlContent('initial html content');
        $Page->setPageTwigEnvironment($PageTwigEnvironment);
        $Page->setCollection($PageCollection);

        $Page->renderPageTwigEnvironment();

        $this->assertEquals($Page->getHtmlContent(), 'new html content');
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

    public function testGenerateTwigDataReturnsTheCorrectData()
    {
        $app = new \Gumdrop\Application();

        $PageCollection = new \Gumdrop\PageCollection($app);
        $PageConfiguration = new \Gumdrop\PageConfiguration();

        $Page = new \Gumdrop\Page($app);
        $Page->setConfiguration($PageConfiguration);
        $Page->setHtmlContent('html content');
        $Page->setCollection($PageCollection);

        $this->assertEquals(
            array(
                'content' => 'html content',
                'page' => $Page->getConfiguration(),
                'pages' => $Page->getCollection()
            ),
            $Page->generateTwigData()
        );

    }
}