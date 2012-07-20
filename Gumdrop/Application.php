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
     * @var \Twig_Loader_Filesystem
     */
    private $TwigLoaderFileSystem;

    /**
     * @var \Twig_Environment
     */
    private $Twig_Environment;


    /**
     * Generates the site
     *
     * @param string $source
     * @param string $destination
     */
    public function generate($destination)
    {
        $PageCollection = $this->FileHandler->listMarkdownFiles();
        $PageCollection = $this->FileHandler->getMarkdownFiles($PageCollection);
        $PageCollection = $this->Engine->convertMarkdownToHtml($PageCollection);
        $PageCollection = $this->Engine->applyTwigLayout($PageCollection);
        $this->Engine->writeHtmFiles($PageCollection, $destination);
    }


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
     * @param \Gumdrop\Engine $Engine
     */
    public function setEngine($Engine)
    {
        $this->Engine = $Engine;
    }

    /**
     * @return \Gumdrop\Engine
     */
    public function getEngine()
    {
        return $this->Engine;
    }

    /**
     * @param \Twig_Loader_Filesystem $TwigLoaderFileSystem
     */
    public function setTwigLoaderFileSystem($TwigLoaderFileSystem)
    {
        $this->TwigLoaderFileSystem = $TwigLoaderFileSystem;
    }

    /**
     * @return \Twig_Loader_Filesystem
     */
    public function getTwigLoaderFileSystem()
    {
        return $this->TwigLoaderFileSystem;
    }

    /**
     * @param \Twig_Environment $Twig_Environment
     */
    public function setTwigEnvironment($Twig_Environment)
    {
        $this->Twig_Environment = $Twig_Environment;
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwigEnvironment()
    {
        return $this->Twig_Environment;
    }

}