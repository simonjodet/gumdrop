<?php
/**
 * Twig files handler
 *
 * @package Gumdrop
 */

namespace Gumdrop;

class TwigFileHandler
{
    private $app;

    public function __construct(\Gumdrop\Application $app)
    {
        $this->app = $app;
    }

    public function renderTwigFiles()
    {
        $twigFiles = $this->app->getFileHandler()->listTwigFiles();
        $SiteTwigEnvironment = $this->app->getTwigEnvironments()->getSiteEnvironment();
        $PageCollection = $this->app->getPageCollection()->exportForTwigRendering();
        foreach ($twigFiles as $twigFile)
        {
            $destination = $this->app->getDestinationLocation();
            $pathinfo = pathinfo($twigFile);
            if (!file_exists($destination . '/' . $pathinfo['dirname']))
            {
                mkdir($destination . '/' . $pathinfo['dirname'], 0777, true);
            }
            $destination_file = $destination . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'];

            $TwigPage = $SiteTwigEnvironment->render(
                $twigFile,
                array(
                    'site' => $this->app->getSiteConfiguration(),
                    'pages' => $PageCollection
                )
            );
            file_put_contents($destination_file, $TwigPage);
        }
    }
}