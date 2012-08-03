<?php

namespace Gumdrop;

/**
 * Gumdrop application
 */
class Application
{
    /**
     * @var \dflydev\markdown\MarkdownParser
     */
    private $MarkdownParser;

    /**
     * @var \Gumdrop\FileHandler
     */
    private $FileHandler;

    /**
     * @var \Gumdrop\Engine
     */
    private $Engine;

    /**
     * @var string Location of the markdown source files
     */
    private $sourceLocation = '';

    /**
     * Generates the site
     *
     * @param string $destination
     */
    public function generate($destination)
    {
        $PageCollection = $this->FileHandler->listMarkdownFiles();
        $PageCollection = $this->FileHandler->getMarkdownFiles($PageCollection);
        $this->Engine->run($PageCollection, $destination);
    }


    /**
     * @param \dflydev\markdown\MarkdownParser $MarkdownParser
     *
     * @codeCoverageIgnore
     */
    public function setMarkdownParser(\dflydev\markdown\MarkdownParser $MarkdownParser)
    {
        $this->MarkdownParser = $MarkdownParser;
    }

    /**
     * @return \dflydev\markdown\MarkdownParser
     * @codeCoverageIgnore
     */
    public function getMarkdownParser()
    {
        return $this->MarkdownParser;
    }

    /**
     * @param \Gumdrop\FileHandler $FileHandler
     *
     * @codeCoverageIgnore
     */
    public function setFileHandler($FileHandler)
    {
        $this->FileHandler = $FileHandler;
    }

    /**
     * @return \Gumdrop\FileHandler
     * @codeCoverageIgnore
     */
    public function getFileHandler()
    {
        return $this->FileHandler;
    }

    /**
     * @param \Gumdrop\Engine $Engine
     *
     * @codeCoverageIgnore
     */
    public function setEngine($Engine)
    {
        $this->Engine = $Engine;
    }

    /**
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
}