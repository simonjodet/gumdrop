<?php
/**
 * Gumdrop dependency injector
 * @package Gumdrop
 */

namespace Gumdrop;

/**
 * Gumdrop application
 */
class Application
{
    /**
     * Application's Markdown parser
     * @var \dflydev\markdown\MarkdownParser
     */
    private $MarkdownParser;

    /**
     * Application's file handler
     * @var \Gumdrop\FileHandler
     */
    private $FileHandler;

    /**
     * Application's engine
     * @var \Gumdrop\Engine
     */
    private $Engine;

    /**
     * TwigEnvironments environment generator
     * @var \Gumdrop\TwigEnvironments
     */
    private $Twig;

    /**
     * Page collection
     * @var \Gumdrop\PageCollection
     */
    private $PageCollection;

    /**
     * Location of the markdown source files
     * @var string
     */
    private $sourceLocation = '';

    /**
     * Location of the generated site
     * @var string
     */
    private $destinationLocation = '';


    /**
     * Set Application's Markdown parser
     *
     * @param \dflydev\markdown\MarkdownParser $MarkdownParser
     *
     * @codeCoverageIgnore
     */
    public function setMarkdownParser(\dflydev\markdown\MarkdownParser $MarkdownParser)
    {
        $this->MarkdownParser = $MarkdownParser;
    }

    /**
     * Get Application's Markdown parser
     * @return \dflydev\markdown\MarkdownParser
     * @codeCoverageIgnore
     */
    public function getMarkdownParser()
    {
        return $this->MarkdownParser;
    }

    /**
     * Set Application's file handler
     *
     * @param \Gumdrop\FileHandler $FileHandler
     *
     * @codeCoverageIgnore
     */
    public function setFileHandler($FileHandler)
    {
        $this->FileHandler = $FileHandler;
    }

    /**
     * Get Application's file handler
     * @return \Gumdrop\FileHandler
     * @codeCoverageIgnore
     */
    public function getFileHandler()
    {
        return $this->FileHandler;
    }

    /**
     * Set Application's engine
     *
     * @param \Gumdrop\Engine $Engine
     *
     * @codeCoverageIgnore
     */
    public function setEngine($Engine)
    {
        $this->Engine = $Engine;
    }

    /**
     * Get Application's engine
     * @return \Gumdrop\Engine
     * @codeCoverageIgnore
     */
    public function getEngine()
    {
        return $this->Engine;
    }

    /**
     * Set location of the markdown source files
     *
     * @param string $sourceLocation
     * @codeCoverageIgnore
     */
    public function setSourceLocation($sourceLocation)
    {
        $this->sourceLocation = $sourceLocation;
    }

    /**
     * Get location of the markdown source files
     * @return string
     * @codeCoverageIgnore
     */
    public function getSourceLocation()
    {
        return $this->sourceLocation;
    }

    /**
     * Set the location of the generated site
     *
     * @param string $destinationLocation
     * @codeCoverageIgnore
     */
    public function setDestinationLocation($destinationLocation)
    {
        $this->destinationLocation = $destinationLocation;
    }

    /**
     * Get the location of the generated site
     * @return string
     * @codeCoverageIgnore
     */
    public function getDestinationLocation()
    {
        return $this->destinationLocation;
    }

    /**
     * Set the Twig environment generator
     *
     * @param \Gumdrop\TwigEnvironments $Twig
     * @codeCoverageIgnore
     */
    public function setTwig($Twig)
    {
        $this->Twig = $Twig;
    }

    /**
     * Get the Twig environment generator
     * @return \Gumdrop\TwigEnvironments
     * @codeCoverageIgnore
     */
    public function getTwig()
    {
        return $this->Twig;
    }

    /**
     * Set the Page collection
     *
     * @param \Gumdrop\PageCollection $PageCollection
     * @codeCoverageIgnore
     */
    public function setPageCollection($PageCollection)
    {
        $this->PageCollection = $PageCollection;
    }

    /**
     * Get the Page collection
     *
     * @return \Gumdrop\PageCollection PageCollection
     * @codeCoverageIgnore
     */
    public function getPageCollection()
    {
        return $this->PageCollection;
    }
}