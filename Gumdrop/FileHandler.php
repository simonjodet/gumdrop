<?php
/**
 * Gumdrop-specific file system operations
 * @package Gumdrop
 */
namespace Gumdrop;

class FileHandler
{
    private $app;

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
                $relative_path = ltrim(str_replace(realpath($location), '', realpath($item)), DIRECTORY_SEPARATOR);
                if (strpos($relative_path, '_') !== 0)
                {
                    if (is_dir($item))
                    {
                        $files = array_merge($files, $this->listFiles($filter_callback, $item));
                    }
                    else
                    {
                        $filter_callback_result = $filter_callback($item);
                        if ($filter_callback_result !== false && !$this->isBlacklisted($item))
                        {
                            $files[] = $filter_callback_result;
                        }
                    }
                }
            }
        }

        return $files;
    }

    private function isBlacklisted($file)
    {
        $conf = $this->app->getSiteConfiguration();
        $blacklist = array();
        if (isset($conf['blacklist']) && is_array($conf['blacklist']))
        {
            $blacklist = $conf['blacklist'];
        }
        $relative_path = ltrim(str_replace($this->app->getSourceLocation(), '', $file), DIRECTORY_SEPARATOR);
        if (in_array($relative_path, $blacklist))
        {
            return true;
        }
        return false;
    }

    public function listMarkdownFiles()
    {
        $filter_callback = function($file)
        {
            $file_info = pathinfo($file);
            if (isset($file_info['extension']) && ($file_info['extension'] == 'md' || $file_info['extension'] == 'markdown'))
            {
                return realpath($file);
            }
            return false;
        };
        return $this->listFiles($filter_callback);
    }

    public function buildPageCollection($files)
    {
        $PageCollection = new \Gumdrop\PageCollection();
        foreach ($files as $file)
        {
            $Page = new \Gumdrop\Page($this->app);
            $Page->setRelativeLocation(ltrim(str_replace(realpath($this->app->getSourceLocation()), '', $file), DIRECTORY_SEPARATOR));
            $Page->setMarkdownContent(file_get_contents($file));
            $PageCollection->offsetSet(null, $Page);
        }
        return $PageCollection;
    }

    public function pageTwigFileExists()
    {
        return file_exists($this->app->getSourceLocation() . '/_layout/page.twig');
    }

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

    public function writeStaticFiles()
    {
        foreach ($this->listStaticFiles() as $file)
        {
            $source = realpath($this->app->getSourceLocation() . DIRECTORY_SEPARATOR . $file);
            $source_pathinfo = pathinfo($source);
            $destination = $this->app->getDestinationLocation() . DIRECTORY_SEPARATOR . $file;
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

    public function listTwigFiles()
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
                $relative_path = ltrim(str_replace(realpath($location), '', realpath($item)), DIRECTORY_SEPARATOR);
                if (strpos($relative_path, '_') !== 0)
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