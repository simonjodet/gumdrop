<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/PageCollection.php';

class PageCollection extends \Gumdrop\Tests\TestCase
{
    public function testPageImplementsTheCorrectInterfaces()
    {
        $this->assertInstanceOf('\Iterator', new \Gumdrop\PageCollection());
        $this->assertInstanceOf('\Countable', new \Gumdrop\PageCollection());
        $this->assertInstanceOf('\ArrayAccess', new \Gumdrop\PageCollection());
    }

    public function testConstructorAcceptAnArrayOfPages()
    {
        $Collection = $this->createAThreePageCollection();

        $Pages = array(
            $Collection['Page1'],
            $Collection['Page2'],
            $Collection['Page3']
        );

        $PageCollection = new \Gumdrop\PageCollection($Pages);

        $this->assertEquals($Pages[0], $PageCollection->offsetGet(0));
        $this->assertEquals($Pages[1], $PageCollection->offsetGet(1));
        $this->assertEquals($Pages[2], $PageCollection->offsetGet(2));
    }

    public function testConstructorAcceptsOnlyArraysOfPages()
    {
        $Collection = $this->createAThreePageCollection();
        $this->setExpectedException(
            'Gumdrop\Exception',
            'Expecting an instance of \Gumdrop\Page'
        );
        $Pages = array(
            'not a page',
            $Collection['Page2'],
            $Collection['Page3']
        );

        new \Gumdrop\PageCollection($Pages);
    }

    public function testExportForTwigCallsTheCorrectPageMethodToBuildArray()
    {
        $PageCollection = new \Gumdrop\PageCollection();

        $Page1 = \Mockery::mock('\Gumdrop\Page');
        $Page1
            ->shouldReceive('exportForTwigRendering')
            ->once()
            ->globally()
            ->andReturn(array('1'));
        $PageCollection[] = $Page1;

        $Page2 = \Mockery::mock('\Gumdrop\Page');
        $Page2
            ->shouldReceive('exportForTwigRendering')
            ->once()
            ->globally()
            ->andReturn(array('2'));
        $PageCollection[] = $Page2;


        $this->assertEquals(
            array(
                array('1'),
                array('2')
            ),
            $PageCollection->exportForTwigRendering()
        );
    }

    public function testOffsetSetAndOffsetGetWorksAsExpected()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $Page = new \Gumdrop\Page($this->getApp());
        $PageCollection->offsetSet(0, $Page);

        $this->assertEquals($Page, $PageCollection->offsetGet(0));
    }

    public function testOffsetGetReturnsNullWhenOffsetDoesNotExist()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $this->assertNull($PageCollection->offsetGet(42));
    }

    public function testOffsetSetExpectsAPageObjectAsValue()
    {
        $this->setExpectedException(
            'Gumdrop\Exception',
            'Expecting an instance of \Gumdrop\Page'
        );

        $PageCollection = new \Gumdrop\PageCollection();
        $PageCollection->offsetSet(0, 'not a Page instance');
    }

    public function testOffsetSetAppendsTheValueIfOffsetIsNull()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $Page = new \Gumdrop\Page($this->getApp());
        $PageCollection->offsetSet(0, $Page);
        $Page->setRelativeLocation('relativeLocation');
        $PageCollection->offsetSet(null, $Page);

        $this->assertEquals($Page, $PageCollection->offsetGet(1));
    }

    public function testOffsetExistsBehavesAsExpected()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $Page = new \Gumdrop\Page($this->getApp());
        $PageCollection->offsetSet(2, $Page);

        $this->assertEquals($Page, $PageCollection->offsetGet(2));
        $this->assertTrue($PageCollection->offsetExists(2));
        $this->assertFalse($PageCollection->offsetExists(1));
    }

    public function testOffsetUnsetBehavesAsExpected()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $Page = new \Gumdrop\Page($this->getApp());
        $PageCollection->offsetSet(2, $Page);
        $this->assertTrue($PageCollection->offsetExists(2));
        $PageCollection->offsetUnset(2);
        $this->assertFalse($PageCollection->offsetExists(2));
    }

    public function testCountBehavesAsExpected()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $this->assertEquals(0, $PageCollection->count());
        $Page = new \Gumdrop\Page($this->getApp());
        $PageCollection->offsetSet(2, $Page);
        $this->assertEquals(1, $PageCollection->count());
        $PageCollection->offsetSet(3, $Page);
        $this->assertEquals(2, $PageCollection->count());
        $PageCollection->offsetUnset(2);
        $this->assertEquals(1, $PageCollection->count());
    }

    public function testCurrentReturnsTheFirstElementByDefault()
    {
        $Collection = $this->createAThreePageCollection();

        $this->assertEquals($Collection['Page1'], $Collection['PageCollection']->current());
    }

    public function testNextMovesThePositionUp()
    {
        $Collection = $this->createAThreePageCollection();

        $this->assertEquals($Collection['PageCollection']->current(), $Collection['Page1']);

        $Collection['PageCollection']->next();

        $this->assertEquals($Collection['PageCollection']->current(), $Collection['Page2']);
    }

    public function testKeyReturnsTheCurrentPosition()
    {
        $Collection = $this->createAThreePageCollection();

        $this->assertEquals($Collection['PageCollection']->key(), 0);

        $Collection['PageCollection']->next();

        $this->assertEquals($Collection['PageCollection']->key(), 1);
    }

    public function testValidBehavesAsExpected()
    {
        $Collection = $this->createAThreePageCollection();
        $this->assertTrue($Collection['PageCollection']->valid());
        $Collection['PageCollection']->next();
        $this->assertTrue($Collection['PageCollection']->valid());
        $Collection['PageCollection']->next();
        $this->assertTrue($Collection['PageCollection']->valid());
        $Collection['PageCollection']->next();
        $this->assertFalse($Collection['PageCollection']->valid());
    }

    public function testRewindBehavesAsExpected()
    {
        $Collection = $this->createAThreePageCollection();
        $this->assertEquals($Collection['PageCollection']->key(), 0);
        $Collection['PageCollection']->next();
        $this->assertEquals($Collection['PageCollection']->key(), 1);
        $Collection['PageCollection']->next();
        $this->assertEquals($Collection['PageCollection']->key(), 2);
        $Collection['PageCollection']->rewind();
        $this->assertEquals($Collection['PageCollection']->key(), 0);
    }


    private function createAThreePageCollection()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $Page1 = new \Gumdrop\Page($this->getApp());
        $Page1->setRelativeLocation('page1');
        $PageCollection->offsetSet(null, $Page1);
        $Page2 = new \Gumdrop\Page($this->getApp());
        $Page2->setRelativeLocation('page2');
        $PageCollection->offsetSet(null, $Page2);
        $Page3 = new \Gumdrop\Page($this->getApp());
        $Page3->setRelativeLocation('page3');
        $PageCollection->offsetSet(null, $Page3);

        return array(
            'PageCollection' => $PageCollection,
            'Page1' => $Page1,
            'Page2' => $Page2,
            'Page3' => $Page3
        );
    }
}
