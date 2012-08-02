<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/PageConfiguration.php';

class PageConfiguration extends \Gumdrop\Tests\TestCase
{
    public function testExtractHeaderCanExtractConfigurationHeaderFromMarkdownContent()
    {
        $PageConfiguration = new \Gumdrop\PageConfiguration();
        $PageConfiguration->extractHeader($this->getValidPageContent());

        $this->assertEquals('value1', $PageConfiguration->conf1);
    }

    public function testExtractHeaderReturnsMarkdownContentWithoutHeader()
    {
        $PageConfiguration = new \Gumdrop\PageConfiguration();
        $markdownContent = $PageConfiguration->extractHeader($this->getValidPageContent());

        $this->assertEquals($this->getStrippedPageContent(), $markdownContent);
    }

    public function testExtractHeaderThrowsAnExceptionIfCouldNotReadConfiguration()
    {
        $this->setExpectedException(
            'Gumdrop\Exception', 'Invalid configuration'
        );
        $PageConfiguration = new \Gumdrop\PageConfiguration();
        $PageConfiguration->extractHeader($this->getInvalidPageContent());
    }

    public function testExtractHeaderSilentlyIgnoresNonExistingConfiguration()
    {
        $PageConfiguration = new \Gumdrop\PageConfiguration();
        $PageConfiguration->extractHeader($this->getNonExistingPageContent());
        $this->assertNull($PageConfiguration->conf1);
    }

    public function testExtractHeaderReturnsContentWhenNoHeaderIsPresent()
    {
        $PageConfiguration = new \Gumdrop\PageConfiguration();
        $markdownContent = $PageConfiguration->extractHeader($this->getNonExistingPageContent());

        $this->assertEquals($this->getNonExistingPageContent(), $markdownContent);
    }

    public function testExtractHeaderIgnoresSuperfluousConfigurationHeaders()
    {
        $PageConfiguration = new \Gumdrop\PageConfiguration();
        $PageConfiguration->extractHeader($this->getDuplicatedPageContent());
        $this->assertEquals('value1', $PageConfiguration->conf1);
    }


    private function getValidPageContent()
    {
        return <<<CONTENT
***
{
    "conf1":"value1"
}
***
Some page content
Some other content
CONTENT;
    }

    private function getStrippedPageContent()
    {
        return <<<CONTENT
Some page content
Some other content
CONTENT;
    }

    private function getInvalidPageContent()
    {
        return <<<CONTENT
***
invalid JSON
***
Some page content
CONTENT;
    }

    private function getNonExistingPageContent()
    {
        return <<<CONTENT
Some page content
CONTENT;
    }

    private function getDuplicatedPageContent()
    {
        return <<<CONTENT
***
{
    "conf1":"value1"
}
***
Some page content
***
{
    "conf1":"value2"
}
***
CONTENT;
    }
}