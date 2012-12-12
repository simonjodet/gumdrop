<?php
/**
 * Engine - Class handling the sequence of steps to render a site
 * @package Gumdrop
 */
namespace Gumdrop;


/**
 * Class handling the sequence of steps to render a site
 */
class Engine
{
    /**
     * @var \Gumdrop\Application
     */
    private $app;

    /**
     * @var \Gumdrop\PageCollection
     */
    public $PageCollection;

    /**
     * @var \Twig_Environment
     */
    public $LayoutTwigEnvironment;

    /**
     * @var \Twig_Environment
     */
    public $PageTwigEnvironment;


    public function run()
    {
        $this->setSourceFallback();
        $this->loadConfigurationFile();
        $this->setConfiguredTimezone();
        $this->setConfiguredDestination();
        $this->setDestinationFallback();
        $this->generatePageCollection();
        $this->generateTwigEnvironments();
        $this->convertPagesToHtml();
        $this->renderPagesTwigEnvironments();
        $this->writeHtmlFiles();
        $this->writeStaticFiles();
        $this->renderTwigFiles();
    }

    public function __construct(\Gumdrop\Application $app)
    {
        $this->app = $app;
    }

    public function setSourceFallback()
    {
        if ($this->app->getSourceLocation() == '')
        {
            //default to the project folder depending on gumdrop
            //example: project/_vendor/simonjodet/gumdrop/Gumdrop/
            $this->app->setSourceLocation(realpath(__DIR__) . '/../../../../');
        }
    }

    public function loadConfigurationFile()
    {
        $this->app->setSiteConfiguration(new \Gumdrop\SiteConfiguration($this->app->getSourceLocation()));
    }

    public function setConfiguredTimezone()
    {
        if ($this->app->getSiteConfiguration()->offsetExists('timezone'))
        {
            date_default_timezone_set($this->app->getSiteConfiguration()->offsetGet('timezone'));
        }
    }

    public function setConfiguredDestination()
    {
        if ($this->app->getSiteConfiguration()->offsetExists('destination'))
        {
            $this->app->setDestinationLocation($this->app->getSiteConfiguration()->offsetGet('destination'));
        }
    }

    public function setDestinationFallback()
    {
        if ($this->app->getDestinationLocation() == '')
        {
            $this->app->setDestinationLocation($this->app->getSourceLocation() . '/_site');
        }
    }

    public function generatePageCollection()
    {
        $PageCollection = $this->app->getFileHandler()->listMarkdownFiles();
        $this->PageCollection = $this->app->getFileHandler()->buildPageCollection($PageCollection);
    }

    public function generateTwigEnvironments()
    {
        $this->LayoutTwigEnvironment = $this->app->getTwigEnvironments()->getLayoutEnvironment();
        $this->PageTwigEnvironment = $this->app->getTwigEnvironments()->getPageEnvironment();
    }

    public function convertPagesToHtml()
    {
        foreach ($this->PageCollection as $key => $Page)
        {
            $this->PageCollection[$key]->setConfiguration(new \Gumdrop\PageConfiguration());
            $this->PageCollection[$key]->convertMarkdownToHtml();
        }
        $this->app->setPageCollection($this->PageCollection);
    }

    public function renderPagesTwigEnvironments()
    {
        foreach ($this->PageCollection as $key => $Page)
        {
            $this->PageCollection[$key]->setLayoutTwigEnvironment($this->LayoutTwigEnvironment);
            $this->PageCollection[$key]->setPageTwigEnvironment($this->PageTwigEnvironment);
            $this->PageCollection[$key]->renderPageTwigEnvironment();
            $this->PageCollection[$key]->renderLayoutTwigEnvironment();
        }
    }

    public function writeHtmlFiles()
    {
        $this->app->getFileHandler()->clearDestinationLocation();

        foreach ($this->PageCollection as $key => $Page)
        {
            $this->PageCollection[$key]->writeHtmlFile($this->app->getDestinationLocation());
        }

        $this->app->setPageCollection($this->PageCollection);
    }

    public function writeStaticFiles()
    {
        $this->app->getFileHandler()->writeStaticFiles();
    }

    public function renderTwigFiles()
    {
        $this->app->getTwigFileHandler()->renderTwigFiles($this->app->getFileHandler()->listTwigFiles());
    }

}
