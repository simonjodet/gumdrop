<?php

namespace Gumdrop;

require_once __DIR__ . '/PageCollection.php';

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
     * Convert Markdown content to HTML pages
     *
     * @param $pages
     *
     * @return \Gumdrop\PageCollection
     */
    public function convertMarkdownToHtml(\Gumdrop\PageCollection $PageCollection)
    {
        foreach ($PageCollection as $key => $Page)
        {
            $PageCollection[$key]->convertMarkdownToHtml();
        }

        return $PageCollection;
    }

    /**
     * Apply a twig layout to the HTML pages
     *
     * @param \Gumdrop\PageCollection $PageCollection
     *
     * @return \Gumdrop\PageCollection
     */
    public function applyTwigLayout(\Gumdrop\PageCollection $PageCollection)
    {
        foreach ($PageCollection as $key => $Page)
        {
            $PageCollection[$key]->applyTwigLayout();
        }

        return $PageCollection;
    }

    /**
     * Write HTML files to their destination
     *
     * @param \Gumdrop\PageCollection $PageCollection
     * @param string                  $destination
     */
    public function writeHtmFiles(\Gumdrop\PageCollection $PageCollection, $destination)
    {
        foreach ($PageCollection as $key => $Page)
        {
            $PageCollection[$key]->writeHtmFiles($destination);
        }
    }
}
