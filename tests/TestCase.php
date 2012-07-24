<?php

namespace Gumdrop\Tests;

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../vendor/mockery/mockery/library/');
date_default_timezone_set('UTC');

require_once 'Mockery/Loader.php';
$loader = new \Mockery\Loader;
$loader->register();

require_once __DIR__ . '/../Gumdrop/Application.php';
require_once __DIR__ . '/../Gumdrop/Exception.php';

define('TMP_FOLDER', '/tmp/');

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \Mockery::close();
    }

    protected function getUniqueId()
    {
        return rand(0, 1000000);
    }

    /**
     * @return \Gumdrop\Application
     */
    protected function getApp()
    {
        return new \Gumdrop\Application();
    }

    public function testNothing()
    {
        $this->assertTrue(true);
    }
}