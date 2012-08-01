<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/PageConfiguration.php';

class PageConfiguration extends \Gumdrop\Tests\TestCase
{
    public function testConstructorCanExtractConfigurationHeaderFromMarkdownContent()
    {
        $PageConfiguration = new \Gumdrop\PageConfiguration($this->getValidPageContent());

        $this->assertEquals('value1', $PageConfiguration->conf1);
    }

    public function testConstructorThrowsAnExceptionIfCouldNotReadConfiguration()
    {
        $this->setExpectedException(
            'Gumdrop\Exception', 'Invalid configuration'
        );
        new \Gumdrop\PageConfiguration($this->getInvalidPageContent());
    }

    public function testConstructorSilentlyIgnoresNonExistingConfiguration()
    {
        $PageConfiguration = new \Gumdrop\PageConfiguration($this->getNonExistingPageContent());
        $this->assertNull($PageConfiguration->conf1);
    }

    public function testConstructorIgnoresSuperfluousConfigurationHeaders()
    {
        $PageConfiguration = new \Gumdrop\PageConfiguration($this->getDuplicatedPageContent());

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