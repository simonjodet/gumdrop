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

    private function listFiles($filter_callback, $location = '')
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
                    $files = array_merge($files, $this->listFiles($filter_callback, $item));
                }
                else
                {
                    $filter_callback_result = $filter_callback($item);
                    if ($filter_callback_result !== false)
                    {
                        $files[] = $filter_callback_result;
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Builds a list of Markdown files recursively
     *
     * @return array
     */
    public function listMarkdownFiles()
    {
        $app = $this->app;
        $filter_callback = function($file) use($app)
        {
            $conf = $app->getSiteConfiguration();
            $blacklist = array();
            if (isset($conf['blacklist']) && is_array($conf['blacklist']))
            {
                $blacklist = $conf['blacklist'];
            }
            $relative_path = ltrim(str_replace(realpath($app->getSourceLocation()), '', $file), DIRECTORY_SEPARATOR);
            if (in_array($relative_path, $blacklist))
            {
                return false;
            }
            $file_info = pathinfo($file);
            if (isset($file_info['extension']) && ($file_info['extension'] == 'md' || $file_info['extension'] == 'markdown'))
            {
                return realpath($file);
            }
            return false;
        };
        return $this->listFiles($filter_callback);
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
            $Page->setLocation(ltrim(str_replace(realpath($this->app->getSourceLocation()), '', $file), DIRECTORY_SEPARATOR));
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
     * @return array
     */
    public function listStaticFiles()
    {
        $app = $this->app;
        $filter_callback = function($item) use($app)
        {
            $item = ltrim(str_replace(realpath($app->getSourceLocation()), '', realpath($item)), DIRECTORY_SEPARATOR);
            $pathinfo = pathinfo($item);
            if ($item != 'conf.json' && strpos($item, '_layout') === false && (!isset($pathinfo['extension']) || ($pathinfo['extension'] != 'md' && $pathinfo['extension'] != 'markdown' && $pathinfo['extension'] != 'twig')))
            {
                return $item;
            }
            return false;
        };
        return $this->listFiles($filter_callback);
    }

    /**
     * Copies static files to the destination folder
     */
    public function copyStaticFiles()
    {
        foreach ($this->listStaticFiles() as $file)
        {
            $source = realpath($this->app->getSourceLocation() . DIRECTORY_SEPARATOR . $file);
            $source_pathinfo = pathinfo($source);
            $destination = realpath($this->app->getDestinationLocation()) . DIRECTORY_SEPARATOR . $file;
            $destination_pathinfo = pathinfo($destination);
            if (!is_dir($destination_pathinfo['dirname']))
            {
                $stats = stat($source_pathinfo['dirname']);
                $mode = octdec('0' . substr(decoct($stats['mode']), -3));
                mkdir($destination_pathinfo['dirname'], $mode, true);
            }
            copy(realpath($this->app->getSourceLocation() . DIRECTORY_SEPARATOR . $file), realpath($this->app->getDestinationLocation()) . DIRECTORY_SEPARATOR . $file);
        }
    }

    /**
     * Returns the list of Twig files
     *
     * @param string $location Used for recursive purposes
     *
     * @return array The list of Twig files
     */
    public function listTwigFiles($location = '')
    {
        $app = $this->app;
        $filter_callback = function($item) use($app)
        {
            $item = ltrim(str_replace(realpath($app->getSourceLocation()), '', realpath($item)), DIRECTORY_SEPARATOR);
            $pathinfo = pathinfo($item);
            if (strpos($item, '_layout') === false && (isset($pathinfo['extension']) && $pathinfo['extension'] == 'twig'))
            {
                return $item;
            }
            return false;
        };
        return $this->listFiles($filter_callback);
    }

    /**
     * Clears the destination location
     *
     * @param string $path Used for recursion, should not be set when using this method
     */
    public function clearDestinationLocation($path = '')
    {
        if ($path == '')
        {
            $path = $this->app->getDestinationLocation();
        }
        foreach (glob($path . '/*') as $file)
        {
            if (is_dir($file))
            {

                $this->clearDestinationLocation($file);
            }
            else
            {
                unlink($file);
            }
        }
        if ($path != $this->app->getDestinationLocation())
        {
            rmdir($path);
        }
    }

    public function getSourceFolderHash($location = '')
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
                    $files = array_merge($files, $this->getSourceFolderHash($item));
                }
                else
                {
                    $files[] = $item;
                }
            }
        }

        if ($location == $this->app->getSourceLocation())
        {
            $content = '';
            sort($files);
            foreach ($files as $file)
            {
                $content .= $file . file_get_contents($file);
            }
            //Using crc32 because it's fast and it's a non-cryptographic use case
            $checksum = crc32($content);
            $checksum = sprintf("%u", $checksum);
            return $checksum;
        }
        return $files;
    }
}