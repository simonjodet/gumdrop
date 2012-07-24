<?php

namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Configuration.php';
require_once __DIR__ . '/../../Gumdrop/Application.php';

class Configuration extends \Gumdrop\Tests\TestCase
{
    public function testConstructorThrowsAnExceptionIfConfigurationFileIsMissing()
    {
        $location = __DIR__ . '/Configuration/no_file/';

        $this->setExpectedException(
            'Gumdrop\Exception', 'Could not find the configuration file at ' . $location . '/conf.json'
        );
        new \Gumdrop\Configuration($location);
    }

    public function testConstructorThrowsAnExceptionIfCouldNotReadConfiguration()
    {
        $location = __DIR__ . '/Configuration/invalid_conf/';
        $this->setExpectedException(
            'Gumdrop\Exception', 'Invalid configuration in ' . $location . '/conf.json'
        );
        new \Gumdrop\Configuration($location);
    }

    public function testConfigurationIsReturnedThroughObjectProperties()
    {
        $location = __DIR__ . '/Configuration/valid_conf/';
        $Configuration = new \Gumdrop\Configuration($location);
        $this->assertEquals($Configuration->conf1, 'value1');
        $this->assertEquals($Configuration->conf2->conf3, 'value3');
    }

    public function testConfigurationPropertiesAreNullWhenKeyIsUnkown()
    {
        $location = __DIR__ . '/Configuration/valid_conf/';
        $Configuration = new \Gumdrop\Configuration($location);

        $this->assertNull($Configuration->conf4);
    }
}
