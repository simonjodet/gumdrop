<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Engine.php';
require_once __DIR__ . '/../../Gumdrop/Configuration.php';
require_once __DIR__ . '/../../Gumdrop/PageConfiguration.php';
require_once __DIR__ . '/../../Gumdrop/PageCollection.php';
require_once __DIR__ . '/../../Gumdrop/Twig.php';
require_once __DIR__ . '/../../vendor/twig/twig/lib/Twig/Autoloader.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/IMarkdownParser.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/MarkdownParser.php';

class Engine extends \Gumdrop\Tests\TestCase
{
    public function testRunBehavesAsExpected()
    {
        \Twig_Autoloader::register();
        $LayoutTwigEnvironmentMock = \Mockery::mock('\Twig_Environment');
        $PageTwigEnvironmentMock = \Mockery::mock('\Twig_Environment');

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
        $Page1
            ->shouldReceive('setCollection')
            ->globally()
            ->ordered()
            ->once()
            ->andReturn('collection1');
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
        $Page2
            ->shouldReceive('setCollection')
            ->globally()
            ->ordered()
            ->once()
            ->andReturn('collection2');
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
        $Page1
            ->shouldReceive('writeHtmFiles')
            ->with('destination')
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
        $Page2
            ->shouldReceive('writeHtmFiles')
            ->globally()
            ->ordered()
            ->with('destination')
            ->once();

        $PageCollection = new \Gumdrop\PageCollection(array(
            $Page1,
            $Page2
        ));


        $FileHandlerMock = \Mockery::mock('\Gumdrop\FileHandler');
        $FileHandlerMock
            ->shouldReceive('listMarkdownFiles')
            ->once()
            ->ordered('generate')
            ->globally()
            ->andReturn($PageCollection);
        $FileHandlerMock
            ->shouldReceive('getMarkdownFiles')
            ->once()
            ->ordered('generate')
            ->globally()
            ->with($PageCollection)
            ->andReturn($PageCollection);
        $FileHandlerMock
            ->shouldReceive('copyStaticFiles')
            ->ordered('generate')
            ->globally()
            ->once();


        $app = $this->getApp();
        $app->setFileHandler($FileHandlerMock);
        $app->setDestinationLocation('destination');

        $TwigMock = \Mockery::mock('\Gumdrop\Twig');
        $TwigMock
            ->shouldReceive('getLayoutEnvironment')
            ->andReturn($LayoutTwigEnvironmentMock)
            ->once();

        $TwigMock
            ->shouldReceive('getPageEnvironment')
            ->andReturn($PageTwigEnvironmentMock)
            ->once();

        $app->setTwig($TwigMock);

        $Engine = new \Gumdrop\Engine($app);
        $Engine->run();
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
            'setCollection',
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
            ->shouldReceive('listMarkdownFiles', 'copyStaticFiles')
            ->byDefault();

        $FileHandlerMock
            ->shouldReceive('getMarkdownFiles')
            ->andReturn($PageCollection);
        $app->setFileHandler($FileHandlerMock);

        $TwigMock = \Mockery::mock('\Gumdrop\Twig');
        $TwigMock
            ->shouldReceive('getLayoutEnvironment')
            ->andReturn($LayoutTwigEnvironmentMock)
            ->once();

        $TwigMock
            ->shouldReceive('getPageEnvironment')
            ->andReturn($PageTwigEnvironmentMock)
            ->once();

        $app->setTwig($TwigMock);

        $Engine = new \Gumdrop\Engine($app);
        $Engine->run();

    }
}