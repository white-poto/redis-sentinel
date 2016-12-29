#!/bin/sh
sh .travis-ci/redis_install.sh
mkdir -p ./data/redis/6379
mkdir -p ./data/redis/6380
mkdir -p ./data/sentinel/26379
mkdir -p ./data/sentinel/26380


./redis/bin/redis-server .travis-ci/etc/redis.6379.conf
./redis/bin/redis-server .travis-ci/etc/redis.6380.conf

./redis/bin/redis-sentinel .travis-ci/etc/sentinel.26379.conf
./redis/bin/redis-sentinel .travis-ci/etc/sentinel.26380.conf


ss -l
