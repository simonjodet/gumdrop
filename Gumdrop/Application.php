<?php
/**
 * Gumdrop application
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
     * Twig environment generator
     * @var \Gumdrop\Twig
     */
    private $Twig;

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
     * Generates the site
     */
    public function generate()
    {
        $PageCollection = $this->FileHandler->listMarkdownFiles();
        $PageCollection = $this->FileHandler->getMarkdownFiles($PageCollection);
        $this->Engine->run($PageCollection);
        $this->FileHandler->copyStaticFiles();
    }


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
     */
    public function setSourceLocation($sourceLocation)
    {
        $this->sourceLocation = $sourceLocation;
    }

    /**
     * Get location of the markdown source files
     * @return string
     */
    public function getSourceLocation()
    {
        return $this->sourceLocation;
    }

    /**
     * Set the location of the generated site
     *
     * @param string $destinationLocation
     */
    public function setDestinationLocation($destinationLocation)
    {
        $this->destinationLocation = $destinationLocation;
    }

    /**
     * Get the location of the generated site
     * @return string
     */
    public function getDestinationLocation()
    {
        return $this->destinationLocation;
    }

    /**
     * Set the Twig environment generator
     *
     * @param \Gumdrop\Twig $Twig
     */
    public function setTwig($Twig)
    {
        $this->Twig = $Twig;
    }

    /**
     * Get the Twig environment generator
     * @return \Gumdrop\Twig
     */
    public function getTwig()
    {
        return $this->Twig;
    }
}