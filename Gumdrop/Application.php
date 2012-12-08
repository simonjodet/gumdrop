<?php
/**
 * Gumdrop dependency injector
 * @package Gumdrop
 */

namespace Gumdrop;

class Application
{
    private $MarkdownParser;

    private $FileHandler;

    private $Engine;

    private $TwigEnvironments;

    private $PageCollection;

    private $TwigFileHandler;

    private $SiteConfiguration;

    private $sourceLocation = '';

    private $destinationLocation = '';


    public function setMarkdownParser(\dflydev\markdown\MarkdownParser $MarkdownParser)
    {
        $this->MarkdownParser = $MarkdownParser;
    }

    public function getMarkdownParser()
    {
        return $this->MarkdownParser;
    }

    public function setFileHandler($FileHandler)
    {
        $this->FileHandler = $FileHandler;
    }

    public function getFileHandler()
    {
        return $this->FileHandler;
    }

    public function setEngine($Engine)
    {
        $this->Engine = $Engine;
    }

    public function getEngine()
    {
        return $this->Engine;
    }

    public function setSourceLocation($sourceLocation)
    {
        $this->sourceLocation = $sourceLocation;
    }

    public function getSourceLocation()
    {
        return $this->sourceLocation;
    }

    public function setDestinationLocation($destinationLocation)
    {
        $this->destinationLocation = $destinationLocation;
    }

    public function getDestinationLocation()
    {
        return $this->destinationLocation;
    }

    public function setTwigEnvironments($Twig)
    {
        $this->TwigEnvironments = $Twig;
    }

    public function getTwigEnvironments()
    {
        return $this->TwigEnvironments;
    }

    public function setPageCollection($PageCollection)
    {
        $this->PageCollection = $PageCollection;
    }

    public function getPageCollection()
    {
        return $this->PageCollection;
    }

    public function setTwigFileHandler($TwigFileHandler)
    {
        $this->TwigFileHandler = $TwigFileHandler;
    }

    public function getTwigFileHandler()
    {
        return $this->TwigFileHandler;
    }

    public function setSiteConfiguration($SiteConfiguration)
    {
        $this->SiteConfiguration = $SiteConfiguration;
    }

    public function getSiteConfiguration()
    {
        return $this->SiteConfiguration;
    }
}