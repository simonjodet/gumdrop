<?php
/**
 * Gumdrop-specific file system operations
 * @package Gumdrop
 */
namespace Gumdrop;

/**
 * Gumdrop-specific file system operations
 */
class FileHandler
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
            $location = $this->app->getSourceLocation();
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
            $Page = new \Gumdrop\Page($this->app);
            $Page->setLocation(ltrim(str_replace(realpath($this->app->getSourceLocation()), '', $file), '/'));
            $Page->setMarkdownContent(file_get_contents($file));
            $PageCollection->offsetSet(null, $Page);
        }
        return $PageCollection;
    }

    /**
     * Checks if the page.twig file exists at the given location
     *
     * @return bool
     */
    public function findPageTwigFile()
    {
        return file_exists($this->app->getSourceLocation() . '/_layout/page.twig');
    }

    /**
     * Returns the recursive list of static files that need to be copied to the destination
     *
     * @param string $location
     *
     * @return array
     */
    public function listStaticFiles($location = '')
    {
        if ($location == '')
        {
            $location = $this->app->getSourceLocation();
        }
        $files = array();
        $items = glob($location . '/*');
        if (is_array($items) && count($items) > 0)
        {
            foreach ($items as $item)
            {
                if (is_dir($item))
                {
                    $files = array_merge($files, $this->listStaticFiles($item));
                }
                else
                {
                    $item = ltrim(str_replace(realpath($this->app->getSourceLocation()), '', realpath($item)), '/');
                    $pathinfo = pathinfo($item);
                    if (strpos($item, '_layout') === false && (!isset($pathinfo['extension']) || ($pathinfo['extension'] != 'md' && $pathinfo['extension'] != 'markdown' && $pathinfo['extension'] != 'twig')))
                    {
                        $files[] = $item;
                    }
                }
            }
        }
        return $files;
    }

    /**
     * Copies static files to the destination folder
     */
    public function copyStaticFiles()
    {
        foreach ($this->listStaticFiles() as $file)
        {
            $source = realpath($this->app->getSourceLocation() . '/' . $file);
            $source_pathinfo = pathinfo($source);
            $destination = realpath($this->app->getDestinationLocation()) . '/' . $file;
            $destination_pathinfo = pathinfo($destination);
            if (!is_dir($destination_pathinfo['dirname']))
            {
                $stats = stat($source_pathinfo['dirname']);
                $mode = octdec('0' . substr(decoct($stats['mode']), -3));
                mkdir($destination_pathinfo['dirname'], $mode, true);
            }
            copy(realpath($this->app->getSourceLocation() . '/' . $file), realpath($this->app->getDestinationLocation()) . '/' . $file);
        }

    }
}