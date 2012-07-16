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
     * Convert Markdown content to HTML pages
     * @param $pages
     * @return array
     */
    public function convertMarkdownToHtml($pages)
    {
        foreach ($pages as $location => $page)
        {
            $pages[$location] = $this->app->getMarkdownParser()->transformMarkdown($page);
        }
        return $pages;
    }

    /**
     * Apply a twig layout to the HTML pages
     * @param $pages
     * @return array
     */
    public function applyTwigLayout($pages)
    {
        foreach ($pages as $location => $page)
        {
            $pages[$location] = $this->app->getTwigEnvironment()->render(
                'page.twig',
                array('content' => $page)
            );
        }
        return $pages;
    }

    /**
     * Write HTML files to their destination
     * @param $pages
     * @param $destination
     */
    public function writeHtmFiles($pages, $destination)
    {
        foreach ($pages as $location => $page)
        {
            $pathinfo = pathinfo($location);
            if (!file_exists($destination . '/' . $pathinfo['dirname']))
            {
                mkdir($destination . '/' . $pathinfo['dirname'], 0777, true);
            }
            $destination_file = $destination . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.htm';
            file_put_contents($destination_file, $page);
        }
    }
}
