<?php

namespace Gumdrop;

require_once __DIR__ . '/PageCollection.php';

/**
 * Contains all file system operations
 */
class FileHandler
{
    /**
     * Location of the files to handle
     * @var string
     */
    private $location;

    /**
     * @var \Gumdrop\Application
     */
    private $app;

    /**
     * @param \Gumdrop\Application $app
     * @param string $location
     */
    public function __construct(\Gumdrop\Application $app, $location = '')
    {
        $this->app = $app;
        $this->location = $location;
    }

    /**
     * Builds a list of Markdown files recursively
     *
     * @param $location
     *
     * @return array
     */
    public function listMarkdownFiles($location = '')
    {
        if ($location == '')
        {
            $location = $this->location;
        }
        $files = array();
        $directories = glob($location . '/*', GLOB_ONLYDIR);
        if (is_array($directories) && count($directories) > 0)
        {
            foreach ($directories as $directory)
            {
                $files = array_merge($files, $this->listMarkdownFiles($directory));
            }
        }
        $files = array_merge($files, glob($location . '/*.{md,markdown}', GLOB_BRACE));
        array_walk($files, function(&$file)
        {
            $file = realpath($file);
        });
        return $files;
    }

    /**
     * Builds a PageCollection out of a list of Markdown files
     *
     * @param $files
     *
     * @return \Gumdrop\PageCollection
     */
    public function getMarkdownFiles($files)
    {
        $PageCollection = new \Gumdrop\PageCollection();
        foreach ($files as $file)
        {
            $Page = new \Gumdrop\Page();
            $Page->setLocation(ltrim(str_replace(realpath($this->location), '', $file), '/'));
            $Page->setMarkdownContent(file_get_contents($file));
            $PageCollection->offsetSet(null, $Page);
        }
        return $PageCollection;
    }

    /**
     * Checks if the page.twig file exists at the given location
     *
     * @param string $location
     *
     * @return bool
     */
    public function findPageTwigFile()
    {
        return file_exists($this->location . '/_layout/page.twig');
    }
}