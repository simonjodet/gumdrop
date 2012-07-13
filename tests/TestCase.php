<?php
namespace tests\units;

require_once __DIR__ . '/../vendor/mageekguy/atoum/classes/autoloader.php';

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../vendor/mockery/mockery/library/');
date_default_timezone_set('UTC');

require_once 'Mockery/Loader.php';
$loader = new \Mockery\Loader;
$loader->register();

require_once __DIR__ . '/../Gumdrop/Application.php';

use \mageekguy\atoum;

define('TMP_FOLDER', '/tmp/');

class TestCase extends atoum\test
{
    public function beforeTestMethod($method)
    {
    }

    public function afterTestMethod($method)
    {
        \Mockery::close();
    }

    protected function getUniqueId()
    {
        return rand(0, 1000000);
    }
}