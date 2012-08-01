<?php

namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Configuration.php';
require_once __DIR__ . '/../../Gumdrop/Application.php';

class Configuration extends \Gumdrop\Tests\TestCase
{
    public function testConfigurationIsReturnedThroughObjectProperties()
    {
        $location = __DIR__ . '/Configuration/valid_conf/';
        $Configuration = new \Gumdrop\Configuration();
        $Configuration->conf1 = 'value1';
        $this->assertEquals($Configuration->conf1, 'value1');

        $Configuration->conf2 = new \StdClass();
        $Configuration->conf2->conf3 = 'value3';
        $this->assertEquals($Configuration->conf2->conf3, 'value3');
    }

    public function testConfigurationPropertiesAreNullWhenKeyIsUnknown()
    {
        $location = __DIR__ . '/Configuration/valid_conf/';
        $Configuration = new \Gumdrop\Configuration($location);

        $this->assertNull($Configuration->conf4);
    }
}
