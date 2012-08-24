<?php

use Behat\Behat\Context\ClosuredContextInterface,
Behat\Behat\Context\TranslatedContextInterface,
Behat\Behat\Context\BehatContext,
Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    private $cssFile;
    private $markdownFile;
    private $source;
    private $destination;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @Given /^I have my test site "([^"]*)"$/
     */
    public function iHaveMyTestSite($source)
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'testFile2.md',
                    'content' => <<<'EOD'
***
{
    "layout":"page2.twig",
    "title":"Page 2 Title"
}
***

{{ page.conf.layout }}

{% for page in pages %}
  {{ page.conf.layout }}-{{ page.conf.title }}
{% endfor %}

# {{ page.conf.title }}
EOD
                ),
                array(
                    'path' => 'style/default.css',
                    'content' => ''
                ),
                array(
                    'path' => 'folder/testFile.markdown',
                    'content' => ''
                )
            )
        ));
        $this->source = $source;
    }

    /**
     * @Given /^It has a CSS file at "([^"]*)"$/
     */
    public function itHasACssFileAt($path)
    {
        if (!file_exists($this->source . '/' . $path))
        {
            throw new \Exception('Missing CSS file');
        }
        $this->cssFile = $path;
    }

    /**
     * @When /^I generate my site in "([^"]*)"$/
     */
    public function iGenerateMySiteIn($destination)
    {
        $destination = __DIR__ . '/../' . $destination;
        if (!file_exists($destination))
        {
            mkdir($destination);
        }
        exec(__DIR__ . '/../../gumdrop.php ' . $this->source . ' ' . $destination, $output, $return_var);
        if ($return_var != 0)
        {
            throw new \Exception('Something went wrong during site generation');
        }
        $this->destination = $destination;
    }

    /**
     * @Then /^I should have the CSS file in the destination folder$/
     */
    public function iShouldHaveTheCssFileInTheDestinationFolder()
    {
        if (!file_exists($this->destination . '/' . $this->cssFile))
        {
            throw new \Exception('The CSS file was not copied');
        }
    }

    /**
     * @Given /^Then delete the destination folder$/
     */
    public function thenDeleteTheDestinationFolder()
    {
        if (file_exists($this->destination))
        {
            exec('rm -rf ' . $this->destination);
        }
    }

    /**
     * @Given /^It has a Markdown file at "([^"]*)"$/
     */
    public function itHasAMarkdownFileAt($path)
    {
        if (!file_exists($this->source . '/' . $path))
        {
            throw new \Exception('Missing Markdown file');
        }
        $this->markdownFile = $path;
    }

    /**
     * @Then /^I should not have the Markdown file in the destination folder$/
     */
    public function iShouldNotHaveTheMarkdownFileInTheDestinationFolder()
    {
        if (file_exists($this->destination . '/' . $this->markdownFile))
        {
            throw new \Exception('The Markdown file was copied');
        }
    }

    /**
     * @Given /^It has a layout folder$/
     */
    public function itHasALayoutFolder()
    {
        if (!file_exists($this->source . '/_layout'))
        {
            throw new \Exception('Missing Layout folder');
        }
    }

    /**
     * @Then /^I should not have the Layout folder in the destination folder$/
     */
    public function iShouldNotHaveTheLayoutFolderInTheDestinationFolder()
    {
        if (file_exists($this->destination . '/_layout'))
        {
            throw new \Exception('The Layout file was copied');
        }
    }
}
