# redis-sentinel
[![Latest Stable Version](https://poser.pugx.org/jenner/redis_sentinel/v/stable)](https://packagist.org/packages/jenner/simple_fork) 
[![Total Downloads](https://poser.pugx.org/jenner/redis_sentinel/downloads)](https://packagist.org/packages/jenner/simple_fork) 
[![Latest Unstable Version](https://poser.pugx.org/jenner/redis_sentinel/v/unstable)](https://packagist.org/packages/jenner/simple_fork) 
[![License](https://poser.pugx.org/jenner/redis_sentinel/license)](https://packagist.org/packages/jenner/simple_fork) 
[![travis](https://travis-ci.org/huyanping/redis-sentinel.svg)](https://travis-ci.org/huyanping/simple-fork-php)

redis-sentinel client for php based on phpredis extension.

## examples
Get Redis master address and create Redis object:
```php
$sentinel = new \Jenner\RedisSentinel\Sentinel();
$sentinel->connect('127.0.0.1', 6379);
$address = $sentinel->getMasterAddrByName('mymaster');

$redis = new Redis();
$redis->connect($address['ip'], $address['port']);
$info = $redis->info();
print_r($info);
```

Create redis-sentinel pool and create Redis object:
```php
$sentinel_pool = new \Jenner\RedisSentinel\SentinelPool();
$sentinel_pool->addSentinel('127.0.0.1', 26379);
$sentinel_pool->addSentinel('127.0.0.1', 26380);

$address = $sentinel_pool->master('mymaster');
print_r($address);

$redis = $sentinel_pool->getRedis('mymaster');
$info = $redis->info();
print_r($info);
```

