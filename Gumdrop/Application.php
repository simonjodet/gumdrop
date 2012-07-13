<?php

namespace Gumdrop;

class Application
{
    /* @var \dflydev\markdown\MarkdownParser */
    private $MarkdownParser;

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
}