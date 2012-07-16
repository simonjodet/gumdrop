<?php

namespace Gumdrop;

/**
 * Contains all file system operations
 */
class FileHandler
{
    /**
     * @param $location
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
     * @return array
     */
    public function getMarkdownFiles($files, $location)
    {
        $contents = array();
        foreach ($files as $file)
        {
            $relative_path = ltrim(str_replace(realpath($location), '', $file), '/');
            $contents[$relative_path] = file_get_contents($file);
        }
        return $contents;
    }
}