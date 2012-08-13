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
     *
     * @param \Gumdrop\PageCollection $PageCollection
     */
    public function run(\Gumdrop\PageCollection $PageCollection)
    {
        $LayoutTwigEnvironment = $this->app->getTwig()->getLayoutEnvironment();
        $PageTwigEnvironment = $this->app->getTwig()->getPageEnvironment();
        foreach ($PageCollection as $key => $Page)
        {
            $PageCollection[$key]->setConfiguration(new \Gumdrop\PageConfiguration());
            $PageCollection[$key]->convertMarkdownToHtml();
            $PageCollection[$key]->setCollection($PageCollection);
        }
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
    }
}
