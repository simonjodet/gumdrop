<?php

namespace Gumdrop;


/**
 * Class handling Markdown files including conversion to HTML
 */
class Engine
{
    /**
     * @var \Gumdrop\Application
     */
    private $app;

    /**
     * @param \Gumdrop\Application $app
     */
    public function __construct(\Gumdrop\Application $app)
    {
        $this->app = $app;
    }

    /**
     * Runs the PageCollection through all the steps of the process
     *
     * @param PageCollection $PageCollection
     * @param $destination
     */
    public function run(\Gumdrop\PageCollection $PageCollection, $destination)
    {
        foreach ($PageCollection as $key => $Page)
        {
            $PageCollection[$key]->setConfiguration(new \Gumdrop\PageConfiguration());
            $PageCollection[$key]->convertMarkdownToHtml();
            $PageCollection[$key]->applyTwigLayout();
            $PageCollection[$key]->writeHtmFiles($destination);
        }
    }
}
