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
 * @method boolean ckquorum(string $master_name)
 * @method boolean checkQuorum(string $master_name)
 * @method boolean flushConfig()
 * @method boolean monitor($master_name, $ip, $port, $quorum)
 * @method boolean remove($master_name)
 * @method boolean set($master_name, $option, $value)
 * @method
 */
class SentinelPool
{
    /**
     * @var Sentinel[]
     */
    protected $sentinels = array();

    /**
     * SentinelPool constructor.
     * @param array $sentinels [['host'=>'host', 'port'=>'port']]
     */
    public function __construct(array $sentinels = array())
    {
        foreach ($sentinels as $sentinel) {
            $this->addSentinel($sentinel['host'], $sentinel['port']);
        }
    }

    /**
     * add sentinel to sentinel pool
     *
     * @param string $host sentinel server host
     * @param int $port sentinel server port
     * @param float $timeout connect timeout in seconds
     * @return bool
     */
    public function addSentinel($host, $port, $timeout = 0.0)
    {
        $sentinel = new Sentinel();
        // if connect to sentinel successfully, add it to sentinels array
        if ($sentinel->connect($host, $port, $timeout)) {
            $this->sentinels[] = $sentinel;
            return true;
        }

        return false;
    }

    /**
     * get Redis object by master name
     *
     * @param $master_name
     * @return \Redis
     * @throws \RedisException
     */
    public function getRedis($master_name)
    {
        $address = $this->getMasterAddrByName($master_name);
        $redis = new \Redis();
        if (!$redis->connect($address['ip'], $address['port'])) {
            throw new \RedisException("connect to redis failed");
        }

        return $redis;
    }

    public function __call($name, $arguments)
    {
        foreach ($this->sentinels as $sentinel) {
            if (!method_exists($sentinel, $name)) {
                throw new \BadMethodCallException("method not exists. method: {$name}");
            }
            try {
                return call_user_func_array(array($sentinel, $name), $arguments);
            } catch (\Exception $e) {
                continue;
            }
        }

        throw new SentinelClientNotConnectException("all sentinel failed");
    }
}