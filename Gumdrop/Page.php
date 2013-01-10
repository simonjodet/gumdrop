<?php
/**
 * Page object representing a page of the website
 * @package Gumdrop
 */

namespace Gumdrop;

class Page
{
    private $app;

    private $relativeLocation;

    public function setRelativeLocation($location)
    {
        $this->relativeLocation = $location;
    }

    public function getRelativeLocation()
    {
        return $this->relativeLocation;
    }

    private $markdownContent;

    public function setMarkdownContent($markdownContent)
    {
        $this->markdownContent = $markdownContent;
    }

    public function getMarkdownContent()
    {
        return $this->markdownContent;
    }

    private $htmlContent;

    public function setHtmlContent($htmlContent)
    {
        $this->htmlContent = $htmlContent;
    }

    public function getHtmlContent()
    {
        return $this->htmlContent;
    }

    private $pageContent;

    public function setPageContent($pageContent)
    {
        $this->pageContent = $pageContent;
    }

    public function getPageContent()
    {
        return $this->pageContent;
    }

    private $configuration;

    public function setConfiguration(\Gumdrop\PageConfiguration $configuration)
    {
        $this->setMarkdownContent($configuration->extractPageHeader($this->getMarkdownContent()));
        $this->configuration = $configuration;
    }

    /**
     * @return \Gumdrop\PageConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @var \Twig_Environment
     */
    private $layoutTwigEnvironment;

    public function setLayoutTwigEnvironment(\Twig_Environment $layoutTwigEnvironment = null)
    {
        $this->layoutTwigEnvironment = $layoutTwigEnvironment;
    }

    public function getLayoutTwigEnvironment()
    {
        return $this->layoutTwigEnvironment;
    }

    /**
     * @var \Twig_Environment
     */
    private $pageTwigEnvironment;

    public function setPageTwigEnvironment($pageTwigEnvironment)
    {
        $this->pageTwigEnvironment = $pageTwigEnvironment;
    }

    public function getPageTwigEnvironment()
    {
        return $this->pageTwigEnvironment;
    }

    public function __construct(\Gumdrop\Application $app)
    {
        $this->app = $app;
    }

    public function convertMarkdownToHtml()
    {
        $this->setHtmlContent($this->app->getMarkdownParser()->transformMarkdown($this->getMarkdownContent()));
    }

    public function renderLayoutTwigEnvironment()
    {
        $this->setPageContent($this->getHtmlContent());
        if (!is_null($this->getLayoutTwigEnvironment())) {
            $twig_layout = null;
            if (isset($this->configuration['layout']) && !is_null($this->configuration['layout'])) {
                $twig_layout = $this->configuration['layout'];
            } elseif ($this->app->getFileHandler()->pageTwigFileExists()) {
                $twig_layout = 'page.twig';
            }
            if (!is_null($twig_layout)) {
                $this->setPageContent(
                    $this->getLayoutTwigEnvironment()->render(
                        $twig_layout,
                        $this->getDataForTwigRendering()
                    )
                );
            }
        }
    }

    public function renderPageTwigEnvironment()
    {
        $this->setHtmlContent(
            $this->getPageTwigEnvironment()->render(
                $this->getHtmlContent(),
                $this->getDataForTwigRendering()
            )
        );
    }

    public function writeHtmlFile($destination)
    {
        $pathinfo = pathinfo($this->getRelativeLocation());
        if (!file_exists($destination . '/' . $pathinfo['dirname'])) {
            mkdir($destination . '/' . $pathinfo['dirname'], 0777, true);
        }
        $conf = $this->getConfiguration();
        if (isset($conf['target_name']) && !empty($conf['target_name'])) {
            $destination_file = $destination . '/' . $pathinfo['dirname'] . '/' . $conf['target_name'];
        } else {
            $destination_file = $destination . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.htm';
        }
        file_put_contents($destination_file, $this->getPageContent());
    }

    public function exportForTwigRendering()
    {
        $path_info = pathinfo($this->getRelativeLocation());
        return array_merge(
            $this->getConfiguration()->extract(),
            array(
                'location' => str_replace(
                    $path_info['basename'],
                    $path_info['filename'] . '.htm',
                    $this->getRelativeLocation()
                ),
                'html' => $this->getHtmlContent(),
                'markdown' => $this->getMarkdownContent()
            )
        );
    }

    private function getDataForTwigRendering()
    {
        return array(
            'content' => $this->getHtmlContent(),
            'page' => $this->exportForTwigRendering(),
            'pages' => $this->app->getPageCollection()->exportForTwigRendering()
        );
    }
}
