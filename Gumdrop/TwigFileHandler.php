<?php
/**
 * Twig files handler
 *
 * @package Gumdrop
 */

namespace Gumdrop;

/**
 * Twig files handler
 */
class TwigFileHandler
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
     * Returns the list of Twig files
     *
     * @param string $location Used for recursive purposes
     *
     * @return array The list of Twig files
     */
    public function listTwigFiles($location = '')
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
                    $files = array_merge($files, $this->listTwigFiles($item));
                }
                else
                {
                    $item = ltrim(str_replace(realpath($this->app->getSourceLocation()), '', realpath($item)), '/');
                    $pathinfo = pathinfo($item);
                    if (strpos($item, '_layout') === false && (isset($pathinfo['extension']) && $pathinfo['extension'] == 'twig'))
                    {
                        $files[] = $item;
                    }
                }
            }
        }
        return $files;
    }

    /**
     * Renders Twig files and write them to the destination folder
     */
    public function renderTwigFiles()
    {
        $twigFiles = $this->listTwigFiles();
        $SiteTwigEnvironment = $this->app->getTwig()->getSiteEnvironment();
        $PageCollection = $this->app->getPageCollection()->exportForTwig();
        foreach ($twigFiles as $twigFile)
        {
            $destination = $this->app->getDestinationLocation();
            $pathinfo = pathinfo($twigFile);
            if (!file_exists($destination . '/' . $pathinfo['dirname']))
            {
                mkdir($destination . '/' . $pathinfo['dirname'], 0777, true);
            }
            $destination_file = $destination . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.htm';

            $TwigPage = $SiteTwigEnvironment->render($twigFile, $PageCollection);
            file_put_contents($destination_file, $TwigPage);
        }


    }
}