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
     * Runs the PageCollection through all the steps of the process
     */
    public function run()
    {
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
            $PageCollection[$key]->writeHtmFiles($this->app->getDestinationLocation());
        }

        $this->app->setPageCollection($PageCollection);
        $this->app->getFileHandler()->copyStaticFiles();
        $this->app->getTwigFileHandler()->renderTwigFiles($this->app->getFileHandler()->listTwigFiles());
    }
}
