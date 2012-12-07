<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Engine.php';
require_once __DIR__ . '/../../Gumdrop/Configuration.php';
require_once __DIR__ . '/../../Gumdrop/SiteConfiguration.php';
require_once __DIR__ . '/../../Gumdrop/PageConfiguration.php';
require_once __DIR__ . '/../../Gumdrop/PageCollection.php';
require_once __DIR__ . '/../../Gumdrop/TwigEnvironments.php';
require_once __DIR__ . '/../../vendor/simonjodet/twig/lib/Twig/Autoloader.php';
require_once __DIR__ . '/../../vendor/simonjodet/markdown/src/dflydev/markdown/IMarkdownParser.php';
require_once __DIR__ . '/../../vendor/simonjodet/markdown/src/dflydev/markdown/MarkdownParser.php';

class Engine extends \Gumdrop\Tests\TestCase
{
    /**
     * @var \FSTestHelper\FSTestHelper
     */
    private $FSTestHelper;

    protected function setUp()
    {
        $this->FSTestHelper = new \FSTestHelper\FSTestHelper();
        $this->FSTestHelper->create(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'conf.json',
                    'content' => '{"timezone":"Europe/Paris"}'
                )
            )
        ));
    }

    public function testRunBehavesAsExpected()
    {
        \Twig_Autoloader::register();
        $LayoutTwigEnvironmentMock = \Mockery::mock('\Twig_Environment');
        $PageTwigEnvironmentMock = \Mockery::mock('\Twig_Environment');

        $FileHandlerMock = \Mockery::mock('\Gumdrop\FileHandler');

        $Page1 = \Mockery::mock('\Gumdrop\Page');
        $Page2 = \Mockery::mock('\Gumdrop\Page');

        $Page1
            ->shouldReceive('setConfiguration')
            ->with(\Mockery::type('\Gumdrop\PageConfiguration'))
            ->globally()
            ->ordered()
            ->once();
        $Page1
            ->shouldReceive('convertMarkdownToHtml')
            ->globally()
            ->ordered()
            ->once();
        $Page2
            ->shouldReceive('setConfiguration')
            ->with(\Mockery::type('\Gumdrop\PageConfiguration'))
            ->globally()
            ->ordered()
            ->once();
        $Page2
            ->shouldReceive('convertMarkdownToHtml')
            ->globally()
            ->ordered()
            ->once();
        $Page1
            ->shouldReceive('setLayoutTwigEnvironment')
            ->with($LayoutTwigEnvironmentMock)
            ->globally()
            ->ordered()
            ->once();
        $Page1
            ->shouldReceive('setPageTwigEnvironment')
            ->with($PageTwigEnvironmentMock)
            ->globally()
            ->ordered()
            ->once();
        $Page1
            ->shouldReceive('renderPageTwigEnvironment')
            ->globally()
            ->ordered()
            ->once();
        $Page1
            ->shouldReceive('renderLayoutTwigEnvironment')
            ->globally()
            ->ordered()
            ->once();
        $Page2
            ->shouldReceive('setLayoutTwigEnvironment')
            ->with($LayoutTwigEnvironmentMock)
            ->globally()
            ->ordered()
            ->once();
        $Page2
            ->shouldReceive('setPageTwigEnvironment')
            ->with($PageTwigEnvironmentMock)
            ->globally()
            ->ordered()
            ->once();
        $Page2
            ->shouldReceive('renderPageTwigEnvironment')
            ->globally()
            ->ordered()
            ->once();
        $Page2
            ->shouldReceive('renderLayoutTwigEnvironment')
            ->globally()
            ->ordered()
            ->once();
        $PageCollection = new \Gumdrop\PageCollection(array(
            $Page1,
            $Page2
        ));

        $FileHandlerMock
            ->shouldReceive('listMarkdownFiles')
            ->once()
            ->ordered()
            ->globally()
            ->andReturn($PageCollection);
        $FileHandlerMock
            ->shouldReceive('getMarkdownFiles')
            ->once()
            ->ordered()
            ->globally()
            ->with($PageCollection)
            ->andReturn($PageCollection);

        $FileHandlerMock
            ->shouldReceive('clearDestinationLocation')
            ->once()
            ->ordered()
            ->globally();


        $Page1
            ->shouldReceive('writeHtmFiles')
            ->with('destination')
            ->globally()
            ->ordered()
            ->once();
        $Page2
            ->shouldReceive('writeHtmFiles')
            ->globally()
            ->ordered()
            ->with('destination')
            ->once();


        $FileHandlerMock
            ->shouldReceive('copyStaticFiles')
            ->ordered()
            ->globally()
            ->once();

        $twigFiles = array(
            'index.twig',
            'folder/index.twig'
        );
        $FileHandlerMock
            ->shouldReceive('listTwigFiles')
            ->once()
            ->globally()
            ->andReturn($twigFiles);

        $app = $this->getApp();
        $app->setFileHandler($FileHandlerMock);

        $TwigFileHandler = \Mockery::mock('\Gumdrop\TwigFileHandler');
        $TwigFileHandler
            ->shouldReceive('renderTwigFiles')
            ->once()
            ->globally()
            ->with($twigFiles);
        $app->setTwigFileHandler($TwigFileHandler);

        $app->setDestinationLocation('destination');

        $TwigEnvironmentsMock = \Mockery::mock('\Gumdrop\TwigEnvironments');
        $TwigEnvironmentsMock
            ->shouldReceive('getLayoutEnvironment')
            ->andReturn($LayoutTwigEnvironmentMock)
            ->once();

        $TwigEnvironmentsMock
            ->shouldReceive('getPageEnvironment')
            ->andReturn($PageTwigEnvironmentMock)
            ->once();
        $app->setTwigEnvironments($TwigEnvironmentsMock);

        $app->setFileHandler($FileHandlerMock);

        $app->setSourceLocation($this->FSTestHelper . '/');

        $Engine = new \Gumdrop\Engine($app);
        $Engine->run();
        $this->assertEquals($PageCollection, $app->getPageCollection());

        $this->assertInstanceOf('\Gumdrop\SiteConfiguration', $app->getSiteConfiguration());
        $this->assertEquals('Europe/Paris', date_default_timezone_get());
    }

    public function testRunDoesNotSetLayoutEnvironmentIfItIsNull()
    {
        $LayoutTwigEnvironmentMock = null;
        $PageTwigEnvironmentMock = \Mockery::mock('\Twig_Environment');

        $Page1 = \Mockery::mock('\Gumdrop\Page');
        $Page1
            ->shouldReceive(
            'setConfiguration',
            'convertMarkdownToHtml',
            'setPageTwigEnvironment',
            'renderPageTwigEnvironment',
            'renderLayoutTwigEnvironment',
            'writeHtmFiles'
        )
            ->byDefault();
        $PageCollection = new \Gumdrop\PageCollection(array($Page1));

        $app = $this->getApp();
        $app->setDestinationLocation('destination');

        $FileHandlerMock = \Mockery::mock('\Gumdrop\FileHandler');
        $FileHandlerMock
            ->shouldReceive('listMarkdownFiles', 'copyStaticFiles', 'clearDestinationLocation')
            ->byDefault();

        $FileHandlerMock
            ->shouldReceive('getMarkdownFiles')
            ->andReturn($PageCollection);
        $FileHandlerMock
            ->shouldReceive('listTwigFiles')
            ->once()
            ->andReturn(array());

        $app->setFileHandler($FileHandlerMock);

        $TwigFileHandler = \Mockery::mock('\Gumdrop\TwigFileHandler');
        $TwigFileHandler
            ->shouldReceive('renderTwigFiles');
        $app->setTwigFileHandler($TwigFileHandler);

        $TwigEnvironmentsMock = \Mockery::mock('\Gumdrop\TwigEnvironments');
        $TwigEnvironmentsMock
            ->shouldReceive('getLayoutEnvironment')
            ->andReturn($LayoutTwigEnvironmentMock)
            ->once();

        $TwigEnvironmentsMock
            ->shouldReceive('getPageEnvironment')
            ->andReturn($PageTwigEnvironmentMock)
            ->once();

        $app->setTwigEnvironments($TwigEnvironmentsMock);
        $app->setSourceLocation($this->FSTestHelper . '/');

        $Engine = new \Gumdrop\Engine($app);
        $Engine->run();
    }

    public function test_run_calls_steps_in_the_correct_order()
    {
        $app = new \Gumdrop\Application();

        /**
         * @var $Engine \Gumdrop\Engine
         */
        $Engine = \Mockery::mock('\Gumdrop\Engine[loadConfigurationFile,setConfiguredTimezone,setConfiguredDestination,setDefaultDestination]', array($app));

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
            ->shouldReceive('setDefaultDestination')
            ->once()
            ->ordered();

        $Engine->new_run();
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

    public function test_setDefaultDestination_sets_the_destination_correctly_if_empty()
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
        $Engine->setDefaultDestination();
    }
}