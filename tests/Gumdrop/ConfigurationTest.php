<?php

namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Configuration.php';
require_once __DIR__ . '/../../Gumdrop/Application.php';

class Configuration extends \Gumdrop\Tests\TestCase
{
    public function testPageImplementsTheCorrectInterfaces()
    {
        $this->assertInstanceOf('\Iterator', new \Gumdrop\Configuration());
        $this->assertInstanceOf('\Countable', new \Gumdrop\Configuration());
        $this->assertInstanceOf('\ArrayAccess', new \Gumdrop\Configuration());
    }

    public function testOffsetSetAndOffsetGetWorksAsExpected()
    {
        $Configuration = new \Gumdrop\Configuration();
        $Configuration->offsetSet(0, 'plop');

        $this->assertEquals('plop', $Configuration->offsetGet(0));
    }

    public function testOffsetExistsBehavesAsExpected()
    {
        $Configuration = new \Gumdrop\Configuration();
        $Configuration->offsetSet(2, 'offset2');

        $this->assertEquals('offset2', $Configuration->offsetGet(2));
        $this->assertTrue($Configuration->offsetExists(2));
        $this->assertFalse($Configuration->offsetExists(1));
    }

    public function testOffsetGetReturnsNullWhenOffsetDoesNotExist()
    {
        $Configuration = new \Gumdrop\Configuration();
        $this->assertNull($Configuration->offsetGet(42));
    }

    public function testOffsetSetAppendsTheValueIfOffsetIsNull()
    {
        $Configuration = new \Gumdrop\Configuration();
        $Configuration->offsetSet(0, 'offset0');
        $Configuration->offsetSet(null, 'offset_not_set');

        $this->assertEquals('offset_not_set', $Configuration->offsetGet(1));
    }

    public function testOffsetUnsetBehavesAsExpected()
    {
        $Configuration = new \Gumdrop\Configuration();
        $Configuration->offsetSet(2, 'offset2');
        $this->assertTrue($Configuration->offsetExists(2));
        $Configuration->offsetUnset(2);
        $this->assertFalse($Configuration->offsetExists(2));
    }

    public function testCountBehavesAsExpected()
    {
        $Configuration = new \Gumdrop\Configuration();
        $this->assertEquals(0, $Configuration->count());
        $Configuration->offsetSet(2, 'plop');
        $this->assertEquals(1, $Configuration->count());
        $Configuration->offsetSet(3, 'plop');
        $this->assertEquals(2, $Configuration->count());
        $Configuration->offsetUnset(2);
        $this->assertEquals(1, $Configuration->count());
    }

    public function testCurrentReturnsTheFirstElementByDefault()
    {
        $Configuration = new \Gumdrop\Configuration();
        $Configuration->offsetSet(0, 'offset0');
        $Configuration->offsetSet(1, 'offset1');

        $this->assertEquals('offset0', $Configuration->current());
    }

    public function testNextMovesThePositionUp()
    {
        $Configuration = $this->generateTestConfiguration();

        $Configuration->next();

        $this->assertEquals('offset1', $Configuration->current());
    }

    public function testKeyReturnsTheCurrentPosition()
    {
        $Configuration = $this->generateTestConfiguration();

        $this->assertEquals(0, $Configuration->key());
        $Configuration->next();
        $this->assertEquals(1, $Configuration->key());
    }

    public function testValidBehavesAsExpected()
    {
        $Configuration = $this->generateTestConfiguration();

        $this->assertTrue($Configuration->valid());
        $Configuration->next();
        $this->assertTrue($Configuration->valid());
        $Configuration->next();
        $this->assertFalse($Configuration->valid());
    }

    public function testRewindBehavesAsExpected()
    {
        $Configuration = $this->generateTestConfiguration();

        $this->assertEquals(0, $Configuration->key());
        $Configuration->next();
        $this->assertEquals(1, $Configuration->key());
        $Configuration->rewind();
        $this->assertEquals(0, $Configuration->key());
    }

    public function testConstructorAcceptAnArray()
    {
        $Configuration = new \Gumdrop\Configuration(
            array(
                0 => 'offset0',
                1 => 'offset1'
            )
        );

        $this->assertEquals('offset0', $Configuration->offsetGet(0));
        $this->assertEquals('offset1', $Configuration->offsetGet(1));
    }

    private function generateTestConfiguration()
    {
        $Configuration = new \Gumdrop\Configuration();
        $Configuration->offsetSet(0, 'offset0');
        $Configuration->offsetSet(1, 'offset1');

        return $Configuration;
    }
}
