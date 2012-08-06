<?php

namespace Gumdrop;

/**
 * Page object representing a page of the website
 */
class Page
{
    /**
     * @var \Gumdrop\Application
     */
    private $app;

    /**
     * @var string
     */
    private $location;

    /**
     * @param string string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @var string
     */
    private $markdownContent;

    /**
     * @param string $markdownContent
     */
    public function setMarkdownContent($markdownContent)
    {
        $this->markdownContent = $markdownContent;
    }

    /**
     * @return string
     */
    public function getMarkdownContent()
    {
        return $this->markdownContent;
    }

    /**
     * @var string
     */
    private $htmlContent;

    /**
     * @param string $htmlContent
     */
    public function setHtmlContent($htmlContent)
    {
        $this->htmlContent = $htmlContent;
    }

    /**
     * @return string
     */
    public function getHtmlContent()
    {
        return $this->htmlContent;
    }

    /**
     * @var \Gumdrop\PageConfiguration
     */
    private $configuration;

    /**
     * @param \Gumdrop\PageConfiguration $configuration
     */
    public function setConfiguration(\Gumdrop\PageConfiguration $configuration)
    {
        $this->setMarkdownContent($configuration->extractHeader($this->getMarkdownContent()));
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
     * @var \Gumdrop\PageCollection
     */
    private $collection;

    /**
     * @param \Gumdrop\PageCollection $collection
     */
    public function setCollection(\Gumdrop\PageCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return \Gumdrop\PageCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @var \Twig_Environment
     */
    private $layoutTwigEnvironment;

    /**
     * @param \Twig_Environment $layoutTwigEnvironment
     */
    public function setLayoutTwigEnvironment(\Twig_Environment $layoutTwigEnvironment)
    {
        $this->layoutTwigEnvironment = $layoutTwigEnvironment;
    }

    /**
     * @return \Twig_Environment
     */
    public function getLayoutTwigEnvironment()
    {
        return $this->layoutTwigEnvironment;
    }

    /**
     * @var \Twig_Environment
     */
    private $pageTwigEnvironment;

    /**
     * @param \Twig_Environment $pageTwigEnvironment
     */
    public function setPageTwigEnvironment($pageTwigEnvironment)
    {
        $this->pageTwigEnvironment = $pageTwigEnvironment;
    }

    /**
     * @return \Twig_Environment
     */
    public function getPageTwigEnvironment()
    {
        return $this->pageTwigEnvironment;
    }

    /* METHODS */
    /**
     * @param \Gumdrop\Application $app
     */
    public function __construct(\Gumdrop\Application $app)
    {
        $this->app = $app;
    }

    /**
     * Converts the Markdown code to HTML
     */
    public function convertMarkdownToHtml()
    {
        $this->setHtmlContent($this->app->getMarkdownParser()->transformMarkdown($this->getMarkdownContent()));
    }

    /**
     * Renders the layout Twig environment of the page
     */
    public function renderLayoutTwigEnvironment()
    {
        $twig_layout = null;
        if (isset($this->configuration['layout']) && !is_null($this->configuration['layout']))
        {
            $twig_layout = $this->configuration['layout'];
        }
        elseif ($this->app->getFileHandler()->findPageTwigFile())
        {
            $twig_layout = 'page.twig';
        }
        if (!is_null($twig_layout))
        {
            $this->setHtmlContent($this->getLayoutTwigEnvironment()->render(
                $twig_layout,
                $this->generateTwigData()
            ));
        }
    }

    /**
     * Renders the page Twig environment of the page
     */
    public function renderPageTwigEnvironment()
    {
        $this->setHtmlContent($this->getPageTwigEnvironment()->render(
            $this->getHtmlContent(),
            $this->generateTwigData()
        ));
    }

    /**
     * Writes the final HTML content to file
     *
     * @param string $destination
     */
    public function writeHtmFiles($destination)
    {
        $pathinfo = pathinfo($this->getLocation());
        if (!file_exists($destination . '/' . $pathinfo['dirname']))
        {
            mkdir($destination . '/' . $pathinfo['dirname'], 0777, true);
        }
        $destination_file = $destination . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.htm';
        file_put_contents($destination_file, $this->getHtmlContent());
    }

    private function generateTwigData()
    {
        return
            array(
                'content' => $this->getHtmlContent(),
                'page' => $this->getConfiguration(),
                'pages' => $this->getCollection()
            );
    }
}
