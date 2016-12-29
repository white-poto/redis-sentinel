#!/bin/sh
SOURCE=https://github.com/antirez/redis/archive/3.2.6.tar.gz
PREFIX=./redis

if [ ! -f redis-server.tar.gz ]; then
     wget $SOURCE -O redis-server.tar.gz
fi

tar zxvf redis-server.tar.gz
cd redis-3.2.6
make && make install
mkdir $PREFIX
mkdir $PREFIX/bin
mkdir $PREFIX/etc
mkdir $PREFIX/var
mkdir $PREFIX/var/log
mkdir $PREFIX/var/data
cp redis.conf $PREFIX/etc/
cp sentinel.conf $PREFIX/etc/
find src/ -type f -executable -exec cp {} $PREFIX/bin \;

echo "install redis success"
rm redis-server.tar.gz -rf

cd ..


