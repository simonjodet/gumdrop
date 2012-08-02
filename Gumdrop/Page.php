<?php

namespace Gumdrop;

/**
 * Page object representing a page of the website
 */
class Page
{
    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */

    private $markdownContent;
    /**
     * @var string
     */
    private $htmlContent;

    /**
     * @var \Gumdrop\PageConfiguration
     */
    private $configuration;

    /**
     * @var \Gumdrop\PageCollection
     */
    private $collection;

    /**
     * @var \Gumdrop\Application
     */
    private $app;

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
     * Runs the twig engine on the HTML content
     */
    public function applyTwigLayout()
    {
        $twig_layout = null;
        if (is_object($this->getConfiguration()) && !is_null($this->getConfiguration()->layout))
        {
            $twig_layout = $this->getConfiguration()->layout;
        }
        elseif ($this->app->getFileHandler()->findPageTwigFile())
        {
            $twig_layout = 'page.twig';
        }
        if (!is_null($twig_layout))
        {
            $this->setHtmlContent($this->app->getTwigEnvironment()->render(
                $twig_layout,
                array(
                    'content' => $this->getHtmlContent(),
                    'conf' => $this->getConfiguration()
                )
            ));
        }
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
     * @param \Gumdrop\PageConfiguration $configuration
     */
    public function setConfiguration(\Gumdrop\PageConfiguration $configuration)
    {
        $configuration->extractHeader($this->getMarkdownContent());
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
}
