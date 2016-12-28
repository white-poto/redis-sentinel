<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2016/12/28
 * Time: 17:44
 */

namespace Jenner\Redis;


class SentinelPool
{
    /**
     * @var Sentinel[]
     */
    protected $sentinels = array();

    public function __construct(array $sentinels = array())
    {
        foreach ($sentinels as $sentinel) {
            $this->sentinels[] = new Sentinel($sentinel['host'], $sentinel['port']);
        }
    }

    public function addSentinel($host, $port)
    {
        $this->sentinels[] = new Sentinel($host, $port);
    }

    public function masters() {
        foreach ($this->sentinels as $sentinel) {
            try {
                return $sentinel->masters();
            } catch (\Exception $e) {
                continue;
            }
        }

        throw new RedisSentinelClientNoConnectionException("all sentinel connect failed");
    }

    public function slaves($master_name) {
        foreach ($this->sentinels as $sentinel) {
            try {
                return $sentinel->slaves($master_name);
            } catch (\Exception $e) {
                continue;
            }
        }

        throw new RedisSentinelClientNoConnectionException("all sentinel connect failed");
    }

    public function isMasterDownByAddress($ip, $port) {
        foreach ($this->sentinels as $sentinel) {
            try {
                return $sentinel->isMasterDownByAddress($ip, $port);
            } catch (\Exception $e) {
                continue;
            }
        }

        throw new RedisSentinelClientNoConnectionException("all sentinel connect failed");
    }

    public function reset($pattern) {
        foreach ($this->sentinels as $sentinel) {
            try {
                return $sentinel->reset($pattern);
            } catch (\Exception $e) {
                continue;
            }
        }

        throw new RedisSentinelClientNoConnectionException("all sentinel connect failed");
    }

    /**
     * @param string $master_name redis master name
     * @return array ["host"=>$host, "port"=>$port]
     * @throws RedisSentinelClientNoConnectionException
     */
    public function getMasterAddressByName($master_name)
    {
        foreach ($this->sentinels as $sentinel) {
            try {
                $data = $sentinel->getMasterAddressByName($master_name);
                $keys = array_keys($data);
                $values = array_values($data);
                return array(
                    'host' => $keys[0],
                    'port' => $values[0],
                );
            } catch (\Exception $e) {
                continue;
            }
        }

        throw new RedisSentinelClientNoConnectionException("all sentinel connect failed");
    }


}