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
     * @var \Gumdrop\MarkdownFilesHandler
     */
    private $MarkdownFilesHandler;

    /**
     * @param \dflydev\markdown\MarkdownParser $MarkdownParser
     */
    public function setMarkdownParser(\dflydev\markdown\MarkdownParser $MarkdownParser)
    {
        $this->MarkdownParser = $MarkdownParser;
    }

    /**
     * @return \dflydev\markdown\MarkdownParser
     */
    public function getMarkdownParser()
    {
        return $this->MarkdownParser;
    }

    /**
     * @param \Gumdrop\FileHandler $FileHandler
     */
    public function setFileHandler($FileHandler)
    {
        $this->FileHandler = $FileHandler;
    }

    /**
     * @return \Gumdrop\FileHandler
     */
    public function getFileHandler()
    {
        return $this->FileHandler;
    }

    /**
     * @param \Gumdrop\MarkdownFilesHandler $MarkdownFilesHandler
     */
    public function setMarkdownFilesHandler($MarkdownFilesHandler)
    {
        $this->MarkdownFilesHandler = $MarkdownFilesHandler;
    }

    /**
     * @return \Gumdrop\MarkdownFilesHandler
     */
    public function getMarkdownFilesHandler()
    {
        return $this->MarkdownFilesHandler;
    }

    /**
     * @param string $source
     * @param string $destination
     */
    public function generate($source, $destination)
    {
        $files = $this->FileHandler->listMarkdownFiles($source);
        $this->MarkdownFilesHandler->convertToHtml($files, $destination);
    }

}