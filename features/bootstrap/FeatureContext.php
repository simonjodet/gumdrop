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
    /**
     * @var \FSTestHelper\FSTestHelper
     */
    private $source;
    /**
     * @var \FSTestHelper\FSTestHelper
     */
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
        $FSTestHelper->cloneTree(__DIR__ . '/../test_site');
        $this->source = $FSTestHelper;
    }

    /**
     * @Given /^It has a CSS file at "([^"]*)"$/
     */
    public function itHasACssFileAt($path)
    {
        if (!file_exists($this->source->getTemporaryPath() . '/' . $path))
        {
            throw new \Exception('Missing CSS file');
        }
        $this->cssFile = $path;
    }

    /**
     * @When /^I generate my site$/
     */
    public function iGenerateMySite()
    {
        $this->destination = new \FSTestHelper\FSTestHelper();
        exec(__DIR__ . '/../../gumdrop.php ' . $this->source->getTemporaryPath() . ' ' . $this->destination->getTemporaryPath(), $output, $return_var);
        if ($return_var != 0)
        {
            print_r($output);
            throw new \Exception('Something went wrong during site generation');
        }
    }

    /**
     * @Then /^I should have the CSS file in the destination folder$/
     */
    public function iShouldHaveTheCssFileInTheDestinationFolder()
    {
        if (!file_exists($this->destination->getTemporaryPath() . '/' . $this->cssFile))
        {
            throw new \Exception('The CSS file was not copied');
        }
    }

    /**
     * @Given /^It has a Markdown file at "([^"]*)"$/
     */
    public function itHasAMarkdownFileAt($path)
    {
        if (!file_exists($this->source->getTemporaryPath() . '/' . $path))
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
        if (file_exists($this->destination->getTemporaryPath() . '/' . $this->markdownFile))
        {
            throw new \Exception('The Markdown file was copied');
        }
    }

    /**
     * @Given /^It has a layout folder$/
     */
    public function itHasALayoutFolder()
    {
        if (!file_exists($this->source->getTemporaryPath() . '/_layout'))
        {
            throw new \Exception('Missing Layout folder');
        }
    }

    /**
     * @Then /^I should not have the Layout folder in the destination folder$/
     */
    public function iShouldNotHaveTheLayoutFolderInTheDestinationFolder()
    {
        if (file_exists($this->destination->getTemporaryPath() . '/_layout'))
        {
            throw new \Exception('The Layout file was copied');
        }
    }
}
