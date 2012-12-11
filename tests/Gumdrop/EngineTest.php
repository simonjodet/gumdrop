<?php
namespace Gumdrop\Tests;

class Engine extends \Gumdrop\Tests\TestCase
{
    public function test_run_calls_steps_in_the_correct_order()
    {
        $app = new \Gumdrop\Application();

        /**
         * @var $Engine \Gumdrop\Engine
         */
        $Engine = \Mockery::mock('\Gumdrop\Engine[loadConfigurationFile,setConfiguredTimezone,setConfiguredDestination,setDestinationFallback,setSourceFallback,generatePageCollection,generateTwigEnvironments,convertPagesToHtml,renderPagesTwigEnvironments,writeHtmlFiles,writeStaticFiles,renderTwigFiles]', array($app));

        $Engine
            ->shouldReceive('loadConfigurationFile')
            ->once()
            ->ordered();
        $Engine
            ->shouldReceive('setConfiguredTimezone')
            ->once()
            ->ordered();
        $Engine
            ->shouldReceive('setConfiguredDestination')
            ->once()
            ->ordered();
        $Engine
            ->shouldReceive('setDestinationFallback')
            ->once()
            ->ordered();
        $Engine
            ->shouldReceive('setSourceFallback')
            ->once()
            ->ordered();
        $Engine
            ->shouldReceive('generatePageCollection')
            ->once()
            ->ordered();
        $Engine
            ->shouldReceive('generateTwigEnvironments')
            ->once()
            ->ordered();
        $Engine
            ->shouldReceive('convertPagesToHtml')
            ->once()
            ->ordered();
        $Engine
            ->shouldReceive('renderPagesTwigEnvironments')
            ->once()
            ->ordered();
        $Engine
            ->shouldReceive('writeHtmlFiles')
            ->once()
            ->ordered();
        $Engine
            ->shouldReceive('writeStaticFiles')
            ->once()
            ->ordered();
        $Engine
            ->shouldReceive('renderTwigFiles')
            ->once()
            ->ordered();

        $Engine->run();
    }

    public function test_loadConfigurationFile_adds_the_loaded_conf_to_the_application()
    {
        $app = \Mockery::mock('\Gumdrop\Application');
        $app
            ->shouldReceive('setSiteConfiguration')
            ->once()
            ->with(\Mockery::type('\Gumdrop\SiteConfiguration'));
        $app
            ->shouldReceive('getSourceLocation')
            ->once()
            ->andReturn($this->createTestFSForStaticAndHtmlFiles());

        $Engine = new \Gumdrop\Engine($app);
        $Engine->loadConfigurationFile();
    }

    public function test_setConfiguredTimezone_sets_the_timezone_according_to_the_conf()
    {
        $SiteConfigurationMock = \Mockery::mock();
        $SiteConfigurationMock
            ->shouldReceive('offsetExists')
            ->once()
            ->with('timezone')
            ->andReturn(true);
        $SiteConfigurationMock
            ->shouldReceive('offsetGet')
            ->once()
            ->with('timezone')
            ->andReturn('UTC');

        $app = \Mockery::mock('\Gumdrop\Application[getSiteConfiguration]');
        $app
            ->shouldReceive('getSiteConfiguration')
            ->andReturn($SiteConfigurationMock);

        $test_path = $this->createTestFSForStaticAndHtmlFiles();
        $app->setSourceLocation($test_path);

        $Engine = new \Gumdrop\Engine($app);
        $Engine->setConfiguredTimezone();

        $this->assertEquals('UTC', date_default_timezone_get());
    }

    public function test_setConfiguredDestination_sets_the_destination_according_to_the_conf()
    {
        $SiteConfigurationMock = \Mockery::mock();
        $SiteConfigurationMock
            ->shouldReceive('offsetExists')
            ->once()
            ->with('destination')
            ->andReturn(true);
        $SiteConfigurationMock
            ->shouldReceive('offsetGet')
            ->once()
            ->with('destination')
            ->andReturn('configured_destination_path');

        $app = \Mockery::mock('\Gumdrop\Application[getSiteConfiguration,setDestinationLocation]');
        $app
            ->shouldReceive('getSiteConfiguration')
            ->andReturn($SiteConfigurationMock);

        $app
            ->shouldReceive('setDestinationLocation')
            ->once()
            ->with('configured_destination_path');

        $test_path = $this->createTestFSForStaticAndHtmlFiles();
        $app->setSourceLocation($test_path);

        $Engine = new \Gumdrop\Engine($app);
        $Engine->setConfiguredDestination();
    }

    public function test_setDestinationFallback_sets_the_destination_correctly_if_empty()
    {
        $app = \Mockery::mock('\Gumdrop\Application[getDestinationLocation,getSourceLocation,setDestinationLocation]');

        $app
            ->shouldReceive('getDestinationLocation')
            ->once()
            ->andReturn('');

        $test_path = $this->createTestFSForStaticAndHtmlFiles();

        $app
            ->shouldReceive('getSourceLocation')
            ->once()
            ->andReturn($test_path);
        $app
            ->shouldReceive('setDestinationLocation')
            ->once()
            ->with($test_path . '/_site');

        $app->setSourceLocation($test_path);

        $Engine = new \Gumdrop\Engine($app);
        $Engine->setDestinationFallback();
    }

    public function test_setSourceFallback_sets_the_source_correctly_if_empty()
    {
        $app = \Mockery::mock('\Gumdrop\Application[getSourceLocation,setSourceLocation]');

        $app
            ->shouldReceive('getSourceLocation')
            ->once()
            ->andReturn('');

        $app
            ->shouldReceive('setSourceLocation')
            ->once()
            ->with(realpath(__DIR__ . '/../../Gumdrop').'/../../../../');

        $Engine = new \Gumdrop\Engine($app);
        $Engine->setSourceFallback();
    }

    public function test_generatePageCollection_loads_the_markdown_pages_collection()
    {
        $PageCollectionMock = \Mockery::mock('\Gumdrop\PageCollection');

        $FileHandlerMock = \Mockery::mock('\Gumdrop\FileHandler');
        $FileHandlerMock
            ->shouldReceive('listMarkdownFiles')
            ->once()
            ->ordered()
            ->andReturn($PageCollectionMock);

        $FileHandlerMock
            ->shouldReceive('buildPageCollection')
            ->once()
            ->ordered()
            ->with($PageCollectionMock)
            ->andReturn($PageCollectionMock);


        $app = \Mockery::mock('\Gumdrop\Application');

        $app
            ->shouldReceive('getFileHandler')
            ->andReturn($FileHandlerMock);

        $Engine = new \Gumdrop\Engine($app);
        $Engine->generatePageCollection();
        $this->assertEquals($PageCollectionMock, $Engine->PageCollection);
    }

    public function test_generateTwigEnvironments_generates_all_twig_envs()
    {
        $TwigEnvironmentsMock = \Mockery::mock('\Gumdrop\TwigEnvironments');

        $TwigEnvironmentsMock
            ->shouldReceive('getLayoutEnvironment')
            ->once()
            ->ordered();

        $TwigEnvironmentsMock
            ->shouldReceive('getPageEnvironment')
            ->once()
            ->ordered();


        $app = \Mockery::mock('\Gumdrop\Application');

        $app
            ->shouldReceive('getTwigEnvironments')
            ->andReturn($TwigEnvironmentsMock);

        $Engine = new \Gumdrop\Engine($app);
        $Engine->generateTwigEnvironments();
    }

    public function test_convertPagesToHtml_loops_on_PageCollection_to_convert_to_html_and_store_it()
    {
        $Page1 = \Mockery::mock('\Gumdrop\Page');
        $Page2 = \Mockery::mock('\Gumdrop\Page');

        $Page1
            ->shouldReceive('setConfiguration')
            ->with(\Mockery::type('\Gumdrop\PageConfiguration'))
            ->ordered()
            ->once();
        $Page1
            ->shouldReceive('convertMarkdownToHtml')
            ->ordered()
            ->once();
        $Page2
            ->shouldReceive('setConfiguration')
            ->with(\Mockery::type('\Gumdrop\PageConfiguration'))
            ->ordered()
            ->once();
        $Page2
            ->shouldReceive('convertMarkdownToHtml')
            ->ordered()
            ->once();

        $PageCollection = new \Gumdrop\PageCollection(array(
            $Page1,
            $Page2
        ));

        $app = new \Gumdrop\Application();
        $Engine = new \Gumdrop\Engine($app);
        $Engine->PageCollection = $PageCollection;
        $Engine->convertPagesToHtml();

        $this->assertEquals($PageCollection, $app->getPageCollection());
    }

    public function test_renderTwigEnvironments_sets_and_render_pages_twig_envs()
    {
        $LayoutTwigEnvironmentMock = \Mockery::mock('\Twig_Environment');
        $PageTwigEnvironmentMock = \Mockery::mock('\Twig_Environment');

        $Page1 = \Mockery::mock('\Gumdrop\Page');
        $Page2 = \Mockery::mock('\Gumdrop\Page');

        $Page1
            ->shouldReceive('setLayoutTwigEnvironment')
            ->with($LayoutTwigEnvironmentMock)
            ->ordered()
            ->once();
        $Page1
            ->shouldReceive('setPageTwigEnvironment')
            ->with($PageTwigEnvironmentMock)
            ->ordered()
            ->once();
        $Page1
            ->shouldReceive('renderPageTwigEnvironment')
            ->ordered()
            ->once();
        $Page1
            ->shouldReceive('renderLayoutTwigEnvironment')
            ->ordered()
            ->once();
        $Page2
            ->shouldReceive('setLayoutTwigEnvironment')
            ->with($LayoutTwigEnvironmentMock)
            ->ordered()
            ->once();
        $Page2
            ->shouldReceive('setPageTwigEnvironment')
            ->with($PageTwigEnvironmentMock)
            ->ordered()
            ->once();
        $Page2
            ->shouldReceive('renderPageTwigEnvironment')
            ->ordered()
            ->once();
        $Page2
            ->shouldReceive('renderLayoutTwigEnvironment')
            ->ordered()
            ->once();
        $PageCollection = new \Gumdrop\PageCollection(array(
            $Page1,
            $Page2
        ));

        $app = new \Gumdrop\Application();
        $Engine = new \Gumdrop\Engine($app);
        $Engine->PageCollection = $PageCollection;
        $Engine->LayoutTwigEnvironment = $LayoutTwigEnvironmentMock;
        $Engine->PageTwigEnvironment = $PageTwigEnvironmentMock;
        $Engine->renderPagesTwigEnvironments();
    }

    public function test_writeHtmlFiles_clears_existing_files_then_write_pages_and_updates_the_collection()
    {
        $app = \Mockery::mock('\Gumdrop\Application');
        $app
            ->shouldReceive('getFileHandler->clearDestinationLocation')
            ->once()
            ->ordered();

        $app
            ->shouldReceive('getDestinationLocation')
            ->twice()
            ->andReturn('destination_path');


        $Page1 = \Mockery::mock('\Gumdrop\Page');
        $Page2 = \Mockery::mock('\Gumdrop\Page');

        $Page1
            ->shouldReceive('writeHtmlFile')
            ->with('destination_path')
            ->ordered()
            ->once();
        $Page2
            ->shouldReceive('writeHtmlFile')
            ->with('destination_path')
            ->ordered()
            ->once();

        $PageCollection = new \Gumdrop\PageCollection(array(
            $Page1,
            $Page2
        ));

        $app
            ->shouldReceive('setPageCollection')
            ->once()
            ->with($PageCollection);

        $Engine = new \Gumdrop\Engine($app);
        $Engine->PageCollection = $PageCollection;
        $Engine->writeHtmlFiles();
    }

    public function test_copyStaticFiles_asks_fileHandler_to_copy_files()
    {
        $app = \Mockery::mock('\Gumdrop\Application');
        $app
            ->shouldReceive('getFileHandler->writeStaticFiles')
            ->once()
            ->ordered();

        $Engine = new \Gumdrop\Engine($app);
        $Engine->writeStaticFiles();
    }

    public function test_renderTwigFiles_renders_the_twig_templates()
    {
        $app = \Mockery::mock('\Gumdrop\Application');
        $app
            ->shouldReceive('getFileHandler->listTwigFiles')
            ->once()
            ->andReturn(array('twig_files'));
        $app
            ->shouldReceive('getTwigFileHandler->renderTwigFiles')
            ->once()
            ->with(array('twig_files'));

        $Engine = new \Gumdrop\Engine($app);
        $Engine->renderTwigFiles();
    }
}