<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 2016/12/29
 * Time: 16:12
 */


require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


$sentinel = new \Jenner\RedisSentinel\Sentinel();
$sentinel->connect('127.0.0.1', 6379);
$address = $sentinel->getMasterAddrByName('mymaster');

$redis = new Redis();
$redis->connect($address['ip'], $address['port']);
$info = $redis->info();
print_r($info);