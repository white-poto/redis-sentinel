<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 2016/12/29
 * Time: 16:15
 */


require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


$sentinel_pool = new \Jenner\RedisSentinel\SentinelPool();
$sentinel_pool->addSentinel('127.0.0.1', 26379);
$sentinel_pool->addSentinel('127.0.0.1', 26380);

$redis = $sentinel_pool->getRedis('mymaster');
$info = $redis->info();
print_r($info);