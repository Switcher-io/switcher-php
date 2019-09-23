<?php

namespace Tests\SwitcherIO;

use PHPUnit\Framework\TestCase;
use SwitcherIO\SwitcherException;
use SwitcherIO\DeadManSwitch;

class DeadManSwitchTest extends TestCase
{
    public function testTestUrlId()
    {
        //the following code should do nothing
        $sw = new DeadManSwitch('test', 'foo');
        $this->assertNull($sw->start());
        $this->assertNull($sw->complete());
        $this->assertNull($sw->pause());
    }

    public function testTestErrorUrlIdStart()
    {
        $this->expectException(SwitcherException::class);

        $sw = new DeadManSwitch('test-error', 'foo');
        $sw->start();
    }

    public function testTestErrorUrlIdComplete()
    {
        $this->expectException(SwitcherException::class);

        $sw = new DeadManSwitch('test-error', 'foo');
        $sw->complete();
    }

    public function testTestErrorUrlIdPause()
    {
        $this->expectException(SwitcherException::class);

        $sw = new DeadManSwitch('test-error', 'foo');
        $sw->pause();
    }
}