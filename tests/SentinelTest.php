<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 2016/12/27
 * Time: 15:22
 */

namespace Jenner\RedisSentinel\Test;


use Jenner\RedisSentinel\Sentinel;
use Jenner\RedisSentinel\SentinelClientNotConnectException;

class SentinelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Sentinel
     */
    private $sentinel;
    private $master_name = 'mymaster';

    public function setUp()
    {
        parent::setUp();
        $this->sentinel = new Sentinel();
        if (!$this->sentinel->connect('127.0.0.1', 26379)) {
            throw new SentinelClientNotConnectException("connect to sentinel server failed");
        }
    }

    public function testPing()
    {
        $this->assertEquals("PONG", $this->sentinel->ping());
    }

    public function testMasters()
    {
        $masters = $this->sentinel->masters();
        $this->assertEquals(1, count($masters));
        $this->assertEquals($this->master_name, $masters[0]['name']);
    }

    public function testMaster()
    {
        $master = $this->sentinel->master($this->master_name);
        $this->assertEquals($this->master_name, $master['name']);
    }

    public function testSlaves()
    {
        $slaves = $this->sentinel->slaves($this->master_name);
        $this->assertEquals(1, count($slaves));
        $this->assertEquals('127.0.0.1', $slaves[0]['ip']);
        $this->assertEquals('6380', $slaves[0]['port']);
    }

    public function testSentinels($master_name)
    {
        $sentinels = $this->sentinel->sentinels($this->master_name);
        $this->assertEquals(1, count($sentinels));
        $this->assertEquals('127.0.0.1', $sentinels[0]['ip']);
        $this->assertEquals(26380, $sentinels[0]['port']);
    }

    public function testGetMasterAddrByName()
    {
        $address = $this->sentinel->getMasterAddrByName($this->master_name);
        $this->assertEquals('127.0.0.1', $address[0]);
        $this->assertEquals(6379, $address[1]);
    }
}