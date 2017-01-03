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

    public function testAll()
    {
        $this->assertEquals('+PONG', $this->sentinel->ping());

        $masters = $this->sentinel->masters();
        $this->assertEquals(1, count($masters));
        $this->assertEquals($this->master_name, $masters[0]['name']);

        $master = $this->sentinel->master($this->master_name);
        $this->assertEquals($this->master_name, $master['name']);

        $slaves = $this->sentinel->slaves($this->master_name);
        $this->assertEquals(2, count($slaves));
        $this->assertEquals('127.0.0.1', $slaves[0]['ip']);
        $this->assertTrue(in_array($slaves[0]['port'], array('6380', '6381')));

        $sentinels = $this->sentinel->sentinels($this->master_name);
        $this->assertEquals(2, count($sentinels));
        $this->assertEquals('127.0.0.1', $sentinels[0]['ip']);
        $this->assertTrue(in_array($sentinels[0]['port'], array('26380', '26381')));

        $address = $this->sentinel->getMasterAddrByName($this->master_name);
        $this->assertEquals('127.0.0.1', $address['ip']);
        $this->assertEquals(6379, $address['port']);

        $this->assertTrue($this->sentinel->flushConfig());

        $this->assertTrue($this->sentinel->checkQuorum($this->master_name));
        $this->assertTrue($this->sentinel->ckquorum($this->master_name));

        $this->assertFalse($this->sentinel->failOver($this->master_name));

        $this->assertTrue($this->sentinel->monitor('add_master', '127.0.0.1', 6382, 2));
        $masters = $this->sentinel->masters();
        $this->assertEquals(2, count($masters));
        $master_names = array();
        foreach ($masters as $master) {
            $master_names[] = $master['name'];
        }
        $this->assertTrue(in_array('add_master', $master_names));
        $this->assertTrue($this->sentinel->remove('add_master'));
        var_dump($this->sentinel->set('add_master', 'down-after-milliseconds', 10000));
    }
}