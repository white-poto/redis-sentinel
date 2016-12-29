<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2016/12/28
 * Time: 17:44
 */

namespace Jenner\RedisSentinel;

/**
 * Class SentinelPool
 * @package Jenner\RedisSentinel
 *
 * @method string ping()
 * @method array masters()
 * @method array master(string $master_name)
 * @method array slaves(string $master_name)
 * @method array sentinels(string $master_name)
 * @method array getMasterAddrByName(string $master_name)
 * @method int reset(string $pattern)
 * @method boolean failOver(string $master_name)
 * @method mixed ckquorum(string $master_name)
 * @method mixed checkQuorum(string $master_name)
 * @method mixed flushConfig()
 */
class SentinelPool
{
    /**
     * @var Sentinel[]
     */
    protected $sentinels = array();

    public function __construct(array $sentinels = array())
    {
        foreach ($sentinels as $sentinel) {
            $this->addSentinel($sentinel['host'], $sentinel['port']);
        }
    }

    public function addSentinel($host, $port)
    {
        $sentinel = new Sentinel();
        // if connect to sentinel successfully, add it to sentinels array
        if ($sentinel->connect($host, $port)) {
            $this->sentinels[] = $sentinel;
            return true;
        }

        return false;
    }

    public function __call($name, $arguments)
    {
        foreach ($this->sentinels as $sentinel) {
            if (!method_exists($sentinel, $name)) {
                throw new \BadMethodCallException("method not exists. method: {$name}");
            }
            try {
                return call_user_func(array($sentinel, $name), $arguments);
            } catch (\Exception $e) {
                continue;
            }
        }

        throw new SentinelClientNotConnectException("all sentinel failed");
    }
}