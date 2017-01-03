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
        try {
            $this->redis->close();
        } catch (\Exception $e) {
        }
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
     * @return string STRING: +PONG on success. Throws a RedisException object on connectivity error.
     */
    public function ping()
    {
        return $this->redis->ping();
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

    /**
     * parse redis response data
     *
     * @param array $data
     * @return array
     */
    private function parseArrayResult(array $data)
    {
        $result = array();
        $count = count($data);
        for ($i = 0; $i < $count;) {
            $record = $data[$i];
            if (is_array($record)) {
                $result[] = $this->parseArrayResult($record);
                $i++;
            } else {
                $result[$record] = $data[$i + 1];
                $i += 2;
            }
        }

        return $result;
    }

    /**
     * Show the state and info of the specified master.
     *
     * @param string $master_name
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
        return $this->parseArrayResult($this->redis->rawCommand('SENTINEL', 'sentinels', $master_name));
    }

    /**
     * Return the ip and port number of the master with that name.
     * If a failover is in progress or terminated successfully
     * for this master it returns the address and port of the promoted slave.
     *
     * @param string $master_name
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
     * @param string $pattern
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
     * @param string $master_name
     * @return boolean
     */
    public function failOver($master_name)
    {
        return $this->redis->rawCommand('SENTINEL', 'failover', $master_name) === 'OK';
    }

    /**
     * @param string $master_name
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
     * @param string $master_name
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
     * @return boolean
     */
    public function flushConfig()
    {
        return $this->redis->rawCommand('SENTINEL', 'flushconfig');
    }

    /**
     * This command tells the Sentinel to start monitoring a new master with the specified name,
     * ip, port, and quorum. It is identical to the sentinel monitor configuration directive
     * in sentinel.conf configuration file, with the difference that you can't use an hostname in as ip,
     * but you need to provide an IPv4 or IPv6 address.
     *
     * @param $master_name
     * @param $ip
     * @param $port
     * @param $quorum
     * @return boolean
     */
    public function monitor($master_name, $ip, $port, $quorum)
    {
        return $this->redis->rawCommand('SENTINEL', 'monitor', $master_name, $ip, $port, $quorum);
    }

    /**
     * is used in order to remove the specified master: the master will no longer be monitored,
     * and will totally be removed from the internal state of the Sentinel,
     * so it will no longer listed by SENTINEL masters and so forth.
     *
     * @param $master_name
     * @return boolean
     */
    public function remove($master_name)
    {
        return $this->redis->rawCommand('SENTINEL', 'remove', $master_name);
    }

    /**
     * The SET command is very similar to the CONFIG SET command of Redis,
     * and is used in order to change configuration parameters of a specific master.
     * Multiple option / value pairs can be specified (or none at all).
     * All the configuration parameters that can be configured via sentinel.conf
     * are also configurable using the SET command.
     *
     * @param $master_name
     * @param $option
     * @param $value
     * @return boolean
     */
    public function set($master_name, $option, $value)
    {
        return $this->redis->rawCommand('SENTINEL', 'set', $master_name, $option, $value);
    }
}
