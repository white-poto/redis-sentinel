<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2016/12/27
 * Time: 14:23
 */

namespace Jenner\RedisSentinel;

class Sentinel
{
    /**
     * @var \Redis
     */
    protected $redis;

    public function __construct()
    {
        $this->redis = new \Redis();
    }

    public function __destruct()
    {
        try{
            $this->redis->close();
        }catch (\Exception $e) {}
    }

    /**
     * @param $host
     * @param int $port
     * @return boolean
     */
    public function connect($host, $port = 26379)
    {
        if (!$this->redis->connect($host, $port)) {
            return false;
        }

        return true;
    }

    /**
     * This command simply returns PONG.
     *
     * @return boolean
     */
    public function ping()
    {
        return $this->redis->ping() === '+PONG';
    }

    /**
     * Show a list of monitored masters and their state.
     *
     * @return array
     */
    public function masters()
    {
        return $this->parseArrayResult($this->redis->rawCommand('SENTINEL', 'masters'));
    }

    private function parseArrayResult(array $data)
    {
        $result = array();
        $count = count($data);
        for ($i = 0; $i < $count; $i += 2) {
            $record = $data[$i];
            if (is_array($record)) {
                $result[] = $this->parseArrayResult($record);
            } else {
                $result[$record] = $data[$i + 1];
            }
        }

        return $result;
    }

    /**
     * Show the state and info of the specified master.
     *
     * @param $master_name
     * @return array
     */
    public function master($master_name)
    {
        return $this->parseArrayResult($this->redis->rawCommand('SENTINEL', 'master', $master_name));
    }

    /**
     * Show a list of slaves for this master, and their state.
     *
     * @param string $master_name
     * @return array
     */
    public function slaves($master_name)
    {
        return $this->parseArrayResult($this->redis->rawCommand('SENTINEL', 'slaves', $master_name));
    }

    /**
     * Show a list of sentinel instances for this master, and their state.
     *
     * @param string $master_name
     * @return array
     */
    public function sentinels($master_name)
    {
        $data = $this->redis->rawCommand('SENTINEL', 'sentinels', $master_name);
        var_dump($data);
        return $this->parseArrayResult($data);
    }

    /**
     * Return the ip and port number of the master with that name.
     * If a failover is in progress or terminated successfully
     * for this master it returns the address and port of the promoted slave.
     *
     * @param $master_name
     * @return array
     */
    public function getMasterAddrByName($master_name)
    {
        $data = $this->redis->rawCommand('SENTINEL', 'get-master-addr-by-name', $master_name);
        return array(
            'ip' => $data[0],
            'port' => $data[1]
        );
    }

    /**
     * This command will reset all the masters with matching name.
     * The pattern argument is a glob-style pattern.
     * The reset process clears any previous state in a master
     * (including a failover in progress), and removes every slave
     * and sentinel already discovered and associated with the master.
     *
     * @param $pattern
     * @return int
     */
    public function reset($pattern)
    {
        return $this->redis->rawCommand('SENTINEL', 'reset', $pattern);
    }

    /**
     * Force a failover as if the master was not reachable,
     * and without asking for agreement to other Sentinels
     * (however a new version of the configuration will be published
     * so that the other Sentinels will update their configurations).
     *
     * @param $master_name
     * @return boolean
     */
    public function failOver($master_name)
    {
        return $this->redis->rawCommand('SENTINEL', 'failover', $master_name) === 'OK';
    }

    /**
     * @param $master_name
     * @return boolean
     */
    public function ckquorum($master_name)
    {
        return $this->checkQuorum($master_name);
    }

    /**
     * Check if the current Sentinel configuration is able to
     * reach the quorum needed to failover a master, and the majority
     * needed to authorize the failover. This command should be
     * used in monitoring systems to check if a Sentinel deployment is ok.
     *
     * @param $master_name
     * @return boolean
     */
    public function checkQuorum($master_name)
    {
        return $this->redis->rawCommand('SENTINEL', 'ckquorum', $master_name);
    }

    /**
     * Force Sentinel to rewrite its configuration on disk,
     * including the current Sentinel state. Normally Sentinel rewrites
     * the configuration every time something changes in its state
     * (in the context of the subset of the state which is persisted on disk across restart).
     * However sometimes it is possible that the configuration file is lost because of
     * operation errors, disk failures, package upgrade scripts or configuration managers.
     * In those cases a way to to force Sentinel to rewrite the configuration file is handy.
     * This command works even if the previous configuration file is completely missing.
     *
     * @return mixed
     */
    public function flushConfig()
    {
        return $this->redis->rawCommand('SENTINEL', 'flushconfig');
    }
}
