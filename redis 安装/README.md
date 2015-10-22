#redis 安装

官方网站下载最新版本redis包

官方下载页:http://www.redis.cn/download.html

linux centos

wget http://download.redis.io/releases/redis-3.0.5.tar.gz

tar zxvf redis-3.0.5.tar.gz

cd redis-3.0.5

make

make install

mkdir -p /etc/redis

cp redis.conf /etc/redis/

vi /etc/redis/redis.conf

修改 daemonize yes---目的使进程在后台运行

修改系统配置文件  

sysctl vm.overcommit_memory=1

使用数字含义：

0，表示内核将检查是否有足够的可用内存供应用进程使用；如果有足够的可用内存，内存申请允许；否则，内存申请失败，并把错误返回给应用进程。

1，表示内核允许分配所有的物理内存，而不管当前的内存状态如何。

2，表示内核允许分配超过所有物理内存和交换空间总和的内存

命令全局化

ln -s /usr/local/bin/redis-server /usr/bin/redis-server

ln -s /usr/local/bin/redis-cli /usr/bin/redis-cli

启动redis

redis-server /etc/redis/redis.conf

链接redis

redis-cli 

默认链接 本地6379端口 

redis-cli -h 127.0.0.1 -p 6380   可以修改主机&端口



