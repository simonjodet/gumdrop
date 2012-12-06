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

    public function testConfigureHandlesTheConfigurationCorrectly()
    {
        $app = \Mockery::mock('\Gumdrop\Application');
        $app
            ->shouldReceive('setSiteConfiguration')
            ->once()
            ->with(\Mockery::type('\Gumdrop\SiteConfiguration'));
        $app
            ->shouldReceive('getSourceLocation')
            ->atLeast()->once()
            ->andReturn($this->createTestFSForStaticAndHtmlFiles());

        $app
            ->shouldReceive('getSiteConfiguration')
            ->andReturn(\Mockery::mock(array('offsetExists' => false)));

        $app->shouldReceive('getDestinationLocation', 'setDestinationLocation')->byDefault();


        $Engine = new \Gumdrop\Engine($app);
        $Engine->configure();
    }

    public function testConfigureSetsTheConfiguredTimeZone()
    {
        $SiteConfigurationMock = $this->getSiteConfigurationMock();

        $app = \Mockery::mock('\Gumdrop\Application[getSiteConfiguration]');
        $app
            ->shouldReceive('getSiteConfiguration')
            ->andReturn($SiteConfigurationMock);

        $test_path = $this->createTestFSForStaticAndHtmlFiles();
        $app->setSourceLocation($test_path);

        $Engine = new \Gumdrop\Engine($app);
        $Engine->configure();

        $this->assertEquals('UTC', date_default_timezone_get());
    }

    public function testConfigurePrioritizeConfiguredDestinationOverCliParameter()
    {
        $SiteConfigurationMock = $this->getSiteConfigurationMock();

        $app = \Mockery::mock('\Gumdrop\Application[getSiteConfiguration,setDestinationLocation]');
        $app
            ->shouldReceive('getSiteConfiguration')
            ->andReturn($SiteConfigurationMock);

        $app
            ->shouldReceive('setDestinationLocation')
            ->once()
            ->with('destination_path');

        $test_path = $this->createTestFSForStaticAndHtmlFiles();
        $app->setSourceLocation($test_path);

        $Engine = new \Gumdrop\Engine($app);
        $Engine->configure();
    }

    public function testConfigureSetsDestinationToSiteSubfolderByDefault()
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
        $SiteConfigurationMock
            ->shouldReceive('offsetExists')
            ->once()
            ->with('destination')
            ->andReturn(false);
        $SiteConfigurationMock
            ->shouldReceive('offsetGet')
            ->with('destination')
            ->never();


        $app = \Mockery::mock('\Gumdrop\Application[getSiteConfiguration,getDestinationLocation,setDestinationLocation]');
        $app
            ->shouldReceive('getSiteConfiguration')
            ->andReturn($SiteConfigurationMock);
        $app
            ->shouldReceive('getDestinationLocation')
            ->once()
            ->andReturn('');

        $test_path = $this->createTestFSForStaticAndHtmlFiles();
        $app->setSourceLocation($test_path);

        $app
            ->shouldReceive('setDestinationLocation')
            ->once()
            ->with($test_path . '/_site');

        $Engine = new \Gumdrop\Engine($app);
        $Engine->configure();
    }

    private function getSiteConfigurationMock()
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

        $SiteConfigurationMock
            ->shouldReceive('offsetExists')
            ->once()
            ->with('destination')
            ->andReturn(true);

        $SiteConfigurationMock
            ->shouldReceive('offsetGet')
            ->once()
            ->with('destination')
            ->andReturn('destination_path');

        return $SiteConfigurationMock;
    }
}