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
        $this->assertEquals("+PONG", $this->sentinel->ping());
        echo 'flushconfig:';
        var_dump($this->sentinel->flushConfig());
        echo 'ck:', PHP_EOL;
        var_dump($this->sentinel->checkQuorum($this->master_name));
        echo 'failOver:', PHP_EOL;
        var_dump($this->sentinel->failOver($this->master_name));
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
        echo 'slaves:', PHP_EOL;
        var_dump($slaves);
        $this->assertEquals(1, count($slaves));
        $this->assertEquals('127.0.0.1', $slaves[0]['ip']);
        $this->assertEquals('6380', $slaves[0]['port']);
    }

    public function testSentinels()
    {
        $sentinels = $this->sentinel->sentinels($this->master_name);
        echo 'sentinels:', PHP_EOL;
        var_dump($sentinels);
        $this->assertEquals(1, count($sentinels));
        $this->assertEquals('127.0.0.1', $sentinels[0]['ip']);
        $this->assertEquals(26380, $sentinels[0]['port']);
    }

    public function testGetMasterAddrByName()
    {
        $address = $this->sentinel->getMasterAddrByName($this->master_name);
        $this->assertEquals('127.0.0.1', $address['ip']);
        $this->assertEquals(6379, $address['port']);
    }
}