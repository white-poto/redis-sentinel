<?php
namespace Jenner\RedisSentinel\Test;

use Jenner\RedisSentinel\SentinelPool;

class SentinelTimeoutTest extends SentinelPoolTest
{
    public function setUp()
    {
        parent::setUp();
        $this->sentinel_pool = new SentinelPool();
        $this->sentinel_pool->addSentinel('127.0.0.1', 26379, 1.1);
        $this->sentinel_pool->addSentinel('127.0.0.1', 26380, 1.2);
        $this->sentinel_pool->addSentinel('127.0.0.1', 26381, 1.3);
    }

    public function testTimeout()
    {
        // run command to populate current sentinel
        $this->assertEquals('+PONG', $this->sentinel_pool->ping());

        $this->assertNotNull($this->sentinel_pool->getCurrentSentinel());

        $this->assertEquals(1.1, $this->sentinel_pool->getCurrentSentinel()->getTimeout());
    }
}