<?php

namespace Gumdrop;

/**
 * Class handling Markdown files including conversion to HTML
 */
class MarkdownFilesHandler
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
     * Convert Markdown content to HTML
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

    public function writeHtmlFiles($files, $destination)
    {
        foreach ($files as $file)
        {
            $destination_file = $destination . '/' . pathinfo($file, PATHINFO_FILENAME) . '.htm';
            file_put_contents($destination_file, $this->app->getMarkdownParser()->transformMarkdown(file_get_contents($file)));
        }
    }
}
