<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Page.php';
require_once __DIR__ . '/../../Gumdrop/PageConfiguration.php';
require_once __DIR__ . '/../../vendor/simonjodet/twig/lib/Twig/Environment.php';
require_once __DIR__ . '/../../vendor/simonjodet/markdown/src/dflydev/markdown/IMarkdownParser.php';
require_once __DIR__ . '/../../vendor/simonjodet/markdown/src/dflydev/markdown/MarkdownParser.php';

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

    public function testRenderLayoutTwigEnvironmentDoesNothingIfEnvironmentIsNull()
    {
        $app = new \Gumdrop\Application();

        $LayoutTwigEnvironment = null;

        $Page = new \Gumdrop\Page($app);
        $Page->setHtmlContent('html content 1');

        $Page->renderLayoutTwigEnvironment();

        $this->assertEquals($Page->getPageContent(), 'html content 1');
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

        $app->setFileHandler($FileHandler);

        $Page = new \Gumdrop\Page($app);
        $Page->setConfiguration($PageConfiguration);
        $Page->setHtmlContent('html content 1');

        $LayoutTwigEnvironment = \Mockery::mock('\Twig_Environment[render]');
        $LayoutTwigEnvironment
            ->shouldReceive('render')
            ->with(
            'page.twig',
            \Mockery::any()
        )
            ->andReturn('twig content 1');

        $Page->setLayoutTwigEnvironment($LayoutTwigEnvironment);
        $app->setPageCollection($PageCollection);

        $Page->renderLayoutTwigEnvironment();

        $this->assertEquals($Page->getPageContent(), 'twig content 1');
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

        $this->assertEquals($Page->getPageContent(), 'html content 1');
    }

    public function testRenderLayoutTwigEnvironmentPreferablyUsesLayoutSetInPageConfiguration()
    {
        $app = new \Gumdrop\Application();

        $PageCollection = new \Gumdrop\PageCollection($app);

        $PageConfiguration = new \Gumdrop\PageConfiguration();
        $PageConfiguration['layout'] = 'twig_layout.twig';

        $Page = new \Gumdrop\Page($app);

        $Page->setConfiguration($PageConfiguration);
        $Page->setHtmlContent('html content 1');

        $LayoutTwigEnvironment = \Mockery::mock('\Twig_Environment[render]');
        $LayoutTwigEnvironment
            ->shouldReceive('render')
            ->with(
            'twig_layout.twig',
            \Mockery::any()
        )
            ->andReturn('twig content 1');

        $Page->setLayoutTwigEnvironment($LayoutTwigEnvironment);
        $app->setPageCollection($PageCollection);

        $Page->renderLayoutTwigEnvironment();
    }

    public function testRenderPageTwigEnvironmentSetsItsResultsToPageHtmlContent()
    {
        $app = new \Gumdrop\Application();

        $PageCollection = new \Gumdrop\PageCollection($app);
        $PageConfiguration = new \Gumdrop\PageConfiguration();

        $Page = new \Gumdrop\Page($app);
        $Page->setConfiguration($PageConfiguration);
        $Page->setHtmlContent('initial html content');

        $PageTwigEnvironment = \Mockery::mock('\Twig_Environment[render]');
        $PageTwigEnvironment
            ->shouldReceive('render')
            ->with(
            'initial html content',
            \Mockery::any()
        )
            ->andReturn('new html content');

        $Page->setPageTwigEnvironment($PageTwigEnvironment);
        $app->setPageCollection($PageCollection);

        $Page->renderPageTwigEnvironment();

        $this->assertEquals($Page->getHtmlContent(), 'new html content');
    }

    public function testRenderPageTwigEnvironmentPassesTheCorrectDataToTemplate()
    {
        $app = new \Gumdrop\Application();

        $PageConfiguration = new \Gumdrop\PageConfiguration();
        $PageConfiguration['layout'] = 'my_layout';
        $PageConfiguration['title'] = 'my_title';

        $PageCollection = \Mockery::mock('\Gumdrop\PageCollection');
        $PageCollection
            ->shouldReceive('exportForTwig')
            ->andReturn(array('exportForTwig'));

        $Page = new \Gumdrop\Page($app);
        $Page->setConfiguration($PageConfiguration);
        $Page->setHtmlContent('initial html content');
        $Page->setLocation('my_folder/my_file.md');
        $Page->setMarkdownContent('markdown content');


        $PageTwigEnvironment = \Mockery::mock('\Twig_Environment[render]');
        $PageTwigEnvironment
            ->shouldReceive('render')
            ->with(
            \Mockery::any(),
            array(
                'content' => 'initial html content',
                'page' => array(
                    'layout' => 'my_layout',
                    'title' => 'my_title',
                    'location' => 'my_folder/my_file.htm',
                    'html' => 'initial html content',
                    'markdown' => 'markdown content'
                ),
                'pages' => array('exportForTwig')
            ));

        $Page->setPageTwigEnvironment($PageTwigEnvironment);
        $app->setPageCollection($PageCollection);
        $Page->renderPageTwigEnvironment();
    }

    public function testExportForTwigReturnsTheCorrectInfo()
    {
        $app = new \Gumdrop\Application();

        $PageConfiguration = new \Gumdrop\PageConfiguration();
        $PageConfiguration['layout'] = 'my_layout';
        $PageConfiguration['title'] = 'my_title';
        $PageConfiguration['html'] = 'this should be overwritten by the page html content';


        $Page = new \Gumdrop\Page($app);
        $Page->setConfiguration($PageConfiguration);
        $Page->setHtmlContent('html content');
        $Page->setLocation('my_folder/my_file.markdown');
        $Page->setMarkdownContent('markdown content');

        $this->assertEquals(
            array(
                'layout' => 'my_layout',
                'title' => 'my_title',
                'location' => 'my_folder/my_file.htm',
                'html' => 'html content',
                'markdown' => 'markdown content'
            ),
            $Page->exportForTwig()
        );
    }

    public function testWriteHtmFilesWritePagesToHtmFiles()
    {
        $app = new \Gumdrop\Application();

        $Page = new \Gumdrop\Page($app);
        $Page->setLocation('folder/file_1_path.md');
        $Page->setPageContent('twig content 1');

        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'folder/file1.md',
                    'content' => ''
                ),
                array(
                    'path' => 'file2.markdown',
                    'content' => ''
                ),
                array(
                    'path' => 'file3.txt',
                    'content' => ''
                )
            )
        ));
        $destination = $FSTestHelper->getTemporaryPath();

        $Page->writeHtmFiles($destination);

        $this->assertStringEqualsFile($destination . '/folder/file_1_path.htm', 'twig content 1');
    }

    public function testWriteHtmFilesWritePagesToSpecifiedFilename()
    {
        $app = new \Gumdrop\Application();

        $Page = new \Gumdrop\Page($app);
        $Page->setLocation('folder/file_1_path.md');
        $Page->setPageContent('twig content 1');
        $Page->setConfiguration(new \Gumdrop\PageConfiguration(array('target_name' => 'file.ext')));

        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'folder/file1.md',
                    'content' => ''
                ),
                array(
                    'path' => 'file2.markdown',
                    'content' => ''
                ),
                array(
                    'path' => 'file3.txt',
                    'content' => ''
                )
            )
        ));
        $destinationFSTestHelper = new \FSTestHelper\FSTestHelper();
        $destination = $destinationFSTestHelper->getTemporaryPath();

        $Page->writeHtmFiles($destination);

        $this->assertStringEqualsFile($destination . '/folder/file.ext', 'twig content 1');
    }
}