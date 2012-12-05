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
}