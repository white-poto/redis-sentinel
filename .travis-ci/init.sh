#!/bin/sh
sh ./redis_install.sh
mkdir -p /data/redis/6379
mkdir -p /data/redis/6380
mkdir -p /data/sentinel/26379
mkdir -p /data/sentinel/26380

redis-server etc/redis.6379.conf
redis-server etc/redis.6380.conf

redis-sentinel etc/sentinel.26379.conf
redis-sentinel etc/sentinel.26380.conf


ss -l