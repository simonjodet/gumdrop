<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/SiteConfiguration.php';

class SiteConfiguration extends \Gumdrop\Tests\TestCase
{
    public function testSiteConfigurationThrowsExceptionOnMissingConfigurationFile()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->create(
            array(
                'folders' => array(),
                'files' => array(
                    array(
                        'path' => 'file.md',
                        'content' => ''
                    )
                )
            )
        );
        $this->setExpectedException(
            'Gumdrop\Exception',
            'Could not find the configuration file at ' . $FSTestHelper . '/conf.json'
        );

        new \Gumdrop\SiteConfiguration($FSTestHelper);
    }

    public function testSiteConfigurationThrowsExceptionOnInvalidConfiguration()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->create(
            array(
                'folders' => array(),
                'files' => array(
                    array(
                        'path' => 'file.md',
                        'content' => ''
                    ),
                    array(
                        'path' => 'conf.json',
                        'content' => 'invalid_json'
                    )
                )
            )
        );
        $this->setExpectedException(
            'Gumdrop\Exception',
            'Invalid configuration in ' . $FSTestHelper . '/conf.json'
        );

        new \Gumdrop\SiteConfiguration($FSTestHelper);
    }
}
