<?php

namespace Gumdrop\tests\units;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Configuration.php';
require_once __DIR__ . '/../../Gumdrop/Application.php';

class Configuration extends \tests\units\TestCase
{
    public function testConstructorThrowsAnExceptionIfConfigurationFileIsMissing()
    {
        $location = __DIR__ . '/Configuration/no_file/';

        $this->assert
            ->exception(function() use ($location)
        {
            new \Gumdrop\Configuration($location);
        })
            ->isInstanceOf('\Exception')
            ->hasMessage('Could not find the configuration file at ' . $location . '/conf.json');
    }

    public function testConstructorThrowsAnExceptionIfCouldNotReadConfiguration()
    {
        $location = __DIR__ . '/Configuration/invalid_conf/';

        $this->assert
            ->exception(function() use ($location)
        {
            new \Gumdrop\Configuration($location);
        })
            ->isInstanceOf('\Exception')
            ->hasMessage('Invalid configuration in ' . $location . '/conf.json');
    }

    public function testConfigurationIsReturnedThroughObjectProperties()
    {
        $location = __DIR__ . '/Configuration/valid_conf/';
        $Configuration = new \Gumdrop\Configuration($location);

        $this->string($Configuration->conf1)->isEqualTo('value1');
        $this->string($Configuration->conf2->conf3)->isEqualTo('value3');
    }

    public function testConfigurationPropertiesAreNullWhenKeyIsUnkown()
    {
        $location = __DIR__ . '/Configuration/valid_conf/';
        $Configuration = new \Gumdrop\Configuration($location);

        $this->variable($Configuration->conf4)->isNull();
    }
}
