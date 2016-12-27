<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 2016/12/27
 * Time: 15:11
 */

namespace Jenner\Redis;


class MasterDiscover
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

    /**
     * @param $name
     * @return array ["host"=>$host, "port"=>$port]
     * @throws RedisSentinelClientNoConnectionException
     */
    public function getMasterByName($name)
    {
        foreach ($this->sentinels as $sentinel) {
            try {
                $data = $sentinel->get_master_addr_by_name($name);
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