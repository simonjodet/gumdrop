<?php
namespace tests\units;

require_once __DIR__ . '/../vendor/mageekguy/atoum/classes/autoloader.php';

use \mageekguy\atoum;

class TestCase extends atoum\test
{
    protected function getUniqueId()
    {
        return rand(0, 1000000);
    }
}