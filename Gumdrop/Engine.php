<?php
/**
 * Engine - Class handling Markdown files including conversion to HTML
 * @package Gumdrop
 */
namespace Gumdrop;


/**
 * Class handling Markdown files including conversion to HTML
 */
class Engine
{
    /**
     * Dependency injector
     * @var \Gumdrop\Application
     */
    private $app;

    /**
     * Constructor
     *
     * @param \Gumdrop\Application $app
     */
    public function __construct(\Gumdrop\Application $app)
    {
        $this->app = $app;
    }

    /**
     * Loads the configuration file
     */
    public function loadConfigurationFile()
    {
        $this->app->setSiteConfiguration(new \Gumdrop\SiteConfiguration($this->app->getSourceLocation()));
    }

    /**
     * Sets the configured timezone
     */
    public function setConfiguredTimezone()
    {
        if ($this->app->getSiteConfiguration()->offsetExists('timezone'))
        {
            date_default_timezone_set($this->app->getSiteConfiguration()->offsetGet('timezone'));
        }
    }

    /**
     * Sets the configured destination over any existing one
     */
    public function setConfiguredDestination()
    {
        if ($this->app->getSiteConfiguration()->offsetExists('destination'))
        {
            $this->app->setDestinationLocation($this->app->getSiteConfiguration()->offsetGet('destination'));
        }
    }

    /**
     * Sets a default destination if it's still empty
     */
    public function setDefaultDestination()
    {
        if ($this->app->getDestinationLocation() == '')
        {
            $this->app->setDestinationLocation($this->app->getSourceLocation() . '/_site');
        }
    }

    /**
     * Runs the different steps of the site generation
     */
    public function new_run()
    {
        $this->loadConfigurationFile();
        $this->setConfiguredTimezone();
        $this->setConfiguredDestination();
        $this->setDefaultDestination();
    }

    /**
     * Runs the PageCollection through all the steps of the process
     */
    public function run()
    {
        $this->new_run();

        $PageCollection = $this->app->getFileHandler()->listMarkdownFiles();
        $PageCollection = $this->app->getFileHandler()->getMarkdownFiles($PageCollection);
        $LayoutTwigEnvironment = $this->app->getTwigEnvironments()->getLayoutEnvironment();
        $PageTwigEnvironment = $this->app->getTwigEnvironments()->getPageEnvironment();
        foreach ($PageCollection as $key => $Page)
        {
            $PageCollection[$key]->setConfiguration(new \Gumdrop\PageConfiguration());
            $PageCollection[$key]->convertMarkdownToHtml();
        }

        $this->app->setPageCollection($PageCollection);

        foreach ($PageCollection as $key => $Page)
        {
            if (!is_null($LayoutTwigEnvironment))
            {
                $PageCollection[$key]->setLayoutTwigEnvironment($LayoutTwigEnvironment);
            }
            $PageCollection[$key]->setPageTwigEnvironment($PageTwigEnvironment);
            $PageCollection[$key]->renderPageTwigEnvironment();
            $PageCollection[$key]->renderLayoutTwigEnvironment();
        }

        $this->app->getFileHandler()->clearDestinationLocation();

        foreach ($PageCollection as $key => $Page)
        {
            $PageCollection[$key]->writeHtmFiles($this->app->getDestinationLocation());
        }

        $this->app->setPageCollection($PageCollection);
        $this->app->getFileHandler()->copyStaticFiles();
        $this->app->getTwigFileHandler()->renderTwigFiles($this->app->getFileHandler()->listTwigFiles());
    }
}
