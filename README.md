# redis-sentinel
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

$redis = $sentinel_pool->getRedis('mymaster');
$info = $redis->info();
print_r($info);
```

