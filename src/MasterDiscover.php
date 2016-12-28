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


}