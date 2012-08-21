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
     * Renders Twig files and write them to the destination folder
     */
    public function renderTwigFiles()
    {
        $twigFiles = $this->app->getFileHandler()->listTwigFiles();
        $SiteTwigEnvironment = $this->app->getTwigEnvironments()->getSiteEnvironment();
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

            $TwigPage = $SiteTwigEnvironment->render($twigFile, array('pages' => $PageCollection));
            file_put_contents($destination_file, $TwigPage);
        }


    }
}