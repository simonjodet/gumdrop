<?php
namespace Gumdrop\tests\units;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/PageCollection.php';

class PageCollection extends \tests\units\TestCase
{
    public function testPageImplementsTheCorrectInterfaces()
    {
        $this->phpClass('\Gumdrop\PageCollection')->hasInterface('Iterator');
        $this->phpClass('\Gumdrop\PageCollection')->hasInterface('Countable');
        $this->phpClass('\Gumdrop\PageCollection')->hasInterface('ArrayAccess');
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

        $this->object($PageCollection->offsetGet(0))->isEqualTo($Pages[0]);
        $this->object($PageCollection->offsetGet(1))->isEqualTo($Pages[1]);
        $this->object($PageCollection->offsetGet(2))->isEqualTo($Pages[2]);
    }

    public function testConstructorAcceptsOnlyArraysOfPages()
    {
        $Collection = $this->createAThreePageCollection();
        $this->assert
            ->exception(function() use($Collection)
        {
            $Pages = array(
                'not a page',
                $Collection['Page2'],
                $Collection['Page3']
            );

            new \Gumdrop\PageCollection($Pages);
        })
            ->isInstanceOf('\Exception')
            ->hasMessage('Expecting an instance of \Gumdrop\Page');
    }

    public function testAddBehavesAsExpected()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $Page = new \Gumdrop\Page($this->getApp());
        $PageCollection->add($Page);

        $this->object($PageCollection->offsetGet(0))->isEqualTo($Page);
    }

    public function testOffsetSetAndOffsetGetWorksAsExpected()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $Page = new \Gumdrop\Page($this->getApp());
        $PageCollection->offsetSet(0, $Page);

        $this->object($PageCollection->offsetGet(0))->isEqualTo($Page);
    }

    public function testOffsetGetReturnsNullWhenOffsetDoesNotExist()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $this->variable($PageCollection->offsetGet(42))->isIdenticalTo(null);
    }

    public function testOffsetSetExpectsAPageObjectAsValue()
    {
        $this->assert
            ->exception(function()
        {
            $PageCollection = new \Gumdrop\PageCollection();
            $PageCollection->offsetSet(0, 'not a Page instance');
        })
            ->isInstanceOf('\Exception')
            ->hasMessage('Expecting an instance of \Gumdrop\Page');
    }

    public function testOffsetSetAppendsTheValueIfOffsetIsNull()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $Page = new \Gumdrop\Page($this->getApp());
        $PageCollection->offsetSet(0, $Page);
        $Page->setLocation('location');
        $PageCollection->offsetSet(null, $Page);

        $this->object($PageCollection->offsetGet(1))->isEqualTo($Page);
    }

    public function testOffsetExistsBehavesAsExpected()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $Page = new \Gumdrop\Page($this->getApp());
        $PageCollection->offsetSet(2, $Page);
        $this->object($PageCollection->offsetGet(2))->isEqualTo($Page);
        $this->boolean($PageCollection->offsetExists(2))->isIdenticalTo(true);
        $this->boolean($PageCollection->offsetExists(1))->isIdenticalTo(false);
    }

    public function testOffsetUnsetBehavesAsExpected()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $Page = new \Gumdrop\Page($this->getApp());
        $PageCollection->offsetSet(2, $Page);
        $this->boolean($PageCollection->offsetExists(2))->isIdenticalTo(true);
        $PageCollection->offsetUnset(2);
        $this->boolean($PageCollection->offsetExists(2))->isIdenticalTo(false);
    }

    public function testCountBehavesAsExpected()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $this->integer($PageCollection->count())->isEqualTo(0);
        $Page = new \Gumdrop\Page($this->getApp());
        $PageCollection->offsetSet(2, $Page);
        $this->integer($PageCollection->count())->isEqualTo(1);
        $PageCollection->offsetSet(3, $Page);
        $this->integer($PageCollection->count())->isEqualTo(2);
        $PageCollection->offsetUnset(2);
        $this->integer($PageCollection->count())->isEqualTo(1);
    }

    public function testCurrentReturnsTheFirstElementByDefault()
    {
        $Collection = $this->createAThreePageCollection();

        $this->object($Collection['PageCollection']->current())->isEqualTo($Collection['Page1']);
    }

    public function testNextMovesThePositionUp()
    {
        $Collection = $this->createAThreePageCollection();

        $this->object($Collection['PageCollection']->current())->isEqualTo($Collection['Page1']);

        $Collection['PageCollection']->next();

        $this->object($Collection['PageCollection']->current())->isEqualTo($Collection['Page2']);
    }

    public function testKeyReturnsTheCurrentPosition()
    {
        $Collection = $this->createAThreePageCollection();

        $this->integer($Collection['PageCollection']->key())->isEqualTo(0);

        $Collection['PageCollection']->next();

        $this->integer($Collection['PageCollection']->key())->isEqualTo(1);
    }

    public function testValidBehavesAsExpected()
    {
        $Collection = $this->createAThreePageCollection();
        $this->boolean($Collection['PageCollection']->valid())->isTrue();
        $Collection['PageCollection']->next();
        $this->boolean($Collection['PageCollection']->valid())->isTrue();
        $Collection['PageCollection']->next();
        $this->boolean($Collection['PageCollection']->valid())->isTrue();
        $Collection['PageCollection']->next();
        $this->boolean($Collection['PageCollection']->valid())->isFalse();
    }

    public function testRewindBehavesAsExpected()
    {
        $Collection = $this->createAThreePageCollection();
        $this->integer($Collection['PageCollection']->key())->isEqualTo(0);
        $Collection['PageCollection']->next();
        $this->integer($Collection['PageCollection']->key())->isEqualTo(1);
        $Collection['PageCollection']->next();
        $this->integer($Collection['PageCollection']->key())->isEqualTo(2);
        $Collection['PageCollection']->rewind();
        $this->integer($Collection['PageCollection']->key())->isEqualTo(0);
    }


    private function createAThreePageCollection()
    {
        $PageCollection = new \Gumdrop\PageCollection();
        $Page1 = new \Gumdrop\Page($this->getApp());
        $Page1->setLocation('page1');
        $PageCollection->offsetSet(null, $Page1);
        $Page2 = new \Gumdrop\Page($this->getApp());
        $Page2->setLocation('page2');
        $PageCollection->offsetSet(null, $Page2);
        $Page3 = new \Gumdrop\Page($this->getApp());
        $Page3->setLocation('page3');
        $PageCollection->offsetSet(null, $Page3);

        return array(
            'PageCollection' => $PageCollection,
            'Page1' => $Page1,
            'Page2' => $Page2,
            'Page3' => $Page3
        );
    }
}