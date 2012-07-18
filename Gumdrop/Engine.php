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
            $PageCollection[$key]->setHtmlContent($this->app->getMarkdownParser()->transformMarkdown($Page->getMarkdownContent()));
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
            $PageCollection[$key]->setHtmlContent($this->app->getTwigEnvironment()->render(
                'page.twig',
                array('content' => $Page->getHtmlContent())
            ));
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
        foreach ($PageCollection as $Page)
        {
            $pathinfo = pathinfo($Page->getLocation());
            if (!file_exists($destination . '/' . $pathinfo['dirname']))
            {
                mkdir($destination . '/' . $pathinfo['dirname'], 0777, true);
            }
            $destination_file = $destination . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.htm';
            file_put_contents($destination_file, $Page->getHtmlContent());
        }
    }
}
