<?php

namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Configuration.php';
require_once __DIR__ . '/../../Gumdrop/GlobalConfiguration.php';
require_once __DIR__ . '/../../Gumdrop/Application.php';

class GlobalConfiguration extends \Gumdrop\Tests\TestCase
{
    public function testConstructorThrowsAnExceptionIfConfigurationFileIsMissing()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'empty',
                    'content' => ''
                )
            )
        ));

        $location = $FSTestHelper->getTemporaryPath();

        $this->setExpectedException(
            'Gumdrop\Exception', 'Could not find the configuration file at ' . $location . '/conf.json'
        );
        new \Gumdrop\GlobalConfiguration($location);
    }

    public function testConstructorThrowsAnExceptionIfCouldNotReadConfiguration()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'conf.json',
                    'content' => 'invalid JSON'
                )
            )
        ));

        $location = $FSTestHelper->getTemporaryPath();

        $this->setExpectedException(
            'Gumdrop\Exception', 'Invalid configuration in ' . $location . '/conf.json'
        );
        new \Gumdrop\GlobalConfiguration($location);
    }

    public function testConfigurationIsReturnedThroughObjectProperties()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'conf.json',
                    'content' => '{"conf1":"value1","conf2":{"conf3":"value3"}}'
                )
            )
        ));

        $location = $FSTestHelper->getTemporaryPath();
        $Configuration = new \Gumdrop\GlobalConfiguration($location);
        $this->assertEquals($Configuration['conf1'], 'value1');
    }
}
