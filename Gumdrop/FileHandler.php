<?php

namespace Gumdrop;

require_once __DIR__ . '/PageCollection.php';

/**
 * Contains all file system operations
 */
class FileHandler
{
    /**
     * @param $location
     *
     * @return array
     */
    public function listMarkdownFiles($location)
    {
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
     * @param $files
     *
     * @return \Gumdrop\PageCollection
     */
    public function getMarkdownFiles($files, $location)
    {
        $PageCollection = new \Gumdrop\PageCollection();
        foreach ($files as $file)
        {
            $Page = new \Gumdrop\Page();
            $Page->setLocation(ltrim(str_replace(realpath($location), '', $file), '/'));
            $Page->setMarkdownContent(file_get_contents($file));
            $PageCollection->offsetSet(null, $Page);
        }
        return $PageCollection;
    }

    /**
     * @param string $location
     *
     * @return bool
     */
    public function findPageTwigFile($location)
    {
        return file_exists($location . '/_layout/page.twig');
    }
}