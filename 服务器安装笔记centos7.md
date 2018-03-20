
# 2017-01-05
# 云服务器环境：CentOS 7.0 64位
cat /etc/centos-release

## 安装完CentOS 7 后做的
- #http://www.centoscn.com/CentosSecurity/CentosSafe/2015/0126/4558.html
- #更改 root 密码
- #新增一个普通帐号
adduser sshuser
passwd sshuser
- ！！！ 这地方要手动输入密码，例如 gongfuNCBD541013   gongfu@NCBD541013
## 防火墙
```
systemctl enable firewalld        # 开机启动 disable
systemctl start firewalld         # 启动, 
firewall-cmd --zone=public --add-port=223/tcp --permanent
firewall-cmd --zone=public --add-port=80/tcp --permanent
firewall-cmd --zone=public --add-port=110/tcp --permanent
firewall-cmd --zone=public --add-port=443/tcp --permanent
firewall-cmd --reload
firewall-cmd --list-all
```
## 禁止 root 使用 ssh 登入;使用非常规的 ssh 端口
```
sed -i '/\#PermitRootLogin.*/a\    PermitRootLogin no' /etc/ssh/sshd_config
sed -i '/\#Port.*/a\    Port 223' /etc/ssh/sshd_config
systemctl restart sshd.service
```
## 关闭SELinux的两种方法
- #http://roclinux.cn/?p=2264

## -------------------载入新的yum源-------------------------------
```
rpm -Uvh ftp://195.220.108.108/linux/centos/7.3.1611/extras/x86_64/Packages/epel-release-7-9.noarch.rpm
rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-7.rpm

#rpm -Uvh http://ftp.iij.ad.jp/pub/linux/fedora/epel/7/x86_64/e/epel-release-7-9.noarch.rpm
#rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-7.rpm


# rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
# rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm

#rpm -Uvh http://ftp.iij.ad.jp/pub/linux/fedora/epel/epel-release-7-9.noarch.rpm
#rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-7.rpm

```
```
yum clean all
yum makecache
yum -y update
```

## sshuser 支持utf8
```
echo 'export LANG=zh_CN.UTF-8' >> /home/sshuser/.bash_profile
```
## centos7.0 没有netstat 和 ifconfig命令问题
```
yum install net-tools
```

## --------------------memcached  服务器端-----------------------------
```
yum -y install memcached.x86_64
```
### 配置
```
sed -i 's/MAXCONN.*$/MAXCONN=\"10240\"/g' /etc/sysconfig/memcached
sed -i 's/CACHESIZE.*$/CACHESIZE=\"512\"/g' /etc/sysconfig/memcached
sed -i 's/OPTIONS.*$/OPTIONS=\" -l 127\.0\.0\.1 -P \/var\/run\/memcached\.pid\"/g' /etc/sysconfig/memcached
```
### 启动
```
systemctl enable memcached.service
systemctl restart memcached.service
```
## ------------svn and rzsz--------------------
```
yum -y install subversion.x86_64
yum -y install lrzsz.x86_64
```

## --------------php安装-------------------------------
```
yum install -y php71.x86_64 php71-php-cli.x86_64 php71-php-common.x86_64 php71-php-devel.x86_64 php71-php-fpm.x86_64 php71-php-json.x86_64 php71-php-mbstring.x86_64 php71-php-mcrypt.x86_64 php71-php-mysqlnd.x86_64 php71-php-opcache.x86_64 php71-php-pdo.x86_64 php71-php-pecl-apcu.x86_64 php71-php-pecl-event.x86_64 php71-php-pecl-igbinary.x86_64 php71-php-pecl-imagick.x86_64 php71-php-pecl-scrypt.x86_64  php71-php-pecl-memcached.x86_64 php71-php-process.x86_64 php71-php-pecl-gearman.x86_64 php71-php-pecl-swoole.x86_64 php71-php-xml.x86_64
```
### 启动
```
systemctl enable php71-php-fpm
systemctl restart php71-php-fpm
```
## -----------------gearman 服务器安装-------------------------
```
yum install -y gearmand.x86_64
```
### 修改配置
```
sed -i 's/\# OPTIONS.*$/OPTIONS=\"-L 127.0.0.1 -p 4730 -t 100 -R\"/g' /etc/sysconfig/gearmand
```
### 启动
```
systemctl enable gearmand.service
systemctl restart gearmand.service
```

## -----------------redis 服务器安装-------------------------
```
yum install -y redis
```
### 修改配置

### 启动
```
systemctl enable redis.service
systemctl restart redis.service
redis-cli.exe -h localhost -p 6347或redis-cli
```

## -----------------mysql 服务器安装-------------------------

#### centOS7的yum源中默认好像是没有mysql的。为了解决这个问题，我们要先下载mysql的repo源。
1. 下载mysql的repo源
```
$ wget http://repo.mysql.com/mysql-community-release-el7-5.noarch.rpm
```
2. 安装mysql-community-release-el7-5.noarch.rpm包
```
$ sudo rpm -ivh mysql-community-release-el7-5.noarch.rpm
```
安装这个包后，会获得两个mysql的yum repo源：
```
/etc/yum.repos.d/mysql-community.repo，

/etc/yum.repos.d/mysql-community-source.repo。
```
3. 安装mysql
```
$ sudo yum install mysql-server
```


### 修改配置
根据步骤安装就可以了，不过安装完成后，没有密码，需要重置密码。
```
重置密码前，首先要登录
$ mysql -u root
登录时有可能报这样的错：ERROR 2002 (HY000): Can't connect to local MySQL server through socket '/var/lib/mysql/mysql.sock' (2)，原因是/var/lib/mysql的访问权限问题。下面的命令把/var/lib/mysql的拥有者改为当前用户：
$ sudo chown -R root:root /var/lib/mysql
然后，重启服务：
$ service mysqld restart
接下来登录重置密码：
正确的root 密码修改：
在mysql 的安装目录中找到 /usr/bin/mysqld_safe 文件， ./mysqld_safe --skip-grant-tables
之后就启动了不用密码的环境：
Mysql -u root

Mysql> update mysql.user set password = password('red') where User='root';

Mysql> flush privileges;

Myusql> quit;
```
### 启动
```
#systemctl restart mysqld.service
#mysql -u root -p
#yxj199019
```
## -------------nginx 安装-------------------------------
```
yum -y install nginx.x86_64
```
### 查看cpu信息配置nginx服务器  
```
# cat /proc/cpuinfo 
```
### 设置
```
#sed -i 's/worker_processes.*$/worker_processes 4;/g' /etc/nginx/nginx.conf
sed -i 's/worker_connections.*$/worker_connections 10240;/g' /etc/nginx/nginx.conf
sed -i '/access_log.*/i\    add_header Access-Control-Allow-Origin \*;' /etc/nginx/nginx.conf
sed -i '/access_log.*/i\    add_header Access-Control-Allow-Headers DNT\,X-Mx-ReqToken\,Keep-Alive\,User-Agent\,X-Requested-With\,If-Modified-Since\,Cache-Control\,Content-Type;' /etc/nginx/nginx.conf
sed -i '/access_log.*/i\    add_header Access-Control-Allow-Methods GET\,POST\,OPTIONS;' /etc/nginx/nginx.conf
sed -i '/access_log.*/i\ ' /etc/nginx/nginx.conf
sed -i '/\keepalive_timeout.*/a\    gzip_disable \"MSIE \[1-6\]\\\.\";' /etc/nginx/nginx.conf
sed -i '/\keepalive_timeout.*/a\    gzip_vary on;' /etc/nginx/nginx.conf
sed -i '/\keepalive_timeout.*/a\    gzip_types text\/json application\/javascript text\/plain application\/x-javascript text\/css application\/xml text\/javascript application\/x-httpd-php image\/jpeg image\/gif image\/png;' /etc/nginx/nginx.conf
sed -i '/\keepalive_timeout.*/a\    gzip_comp_level 6;' /etc/nginx/nginx.conf
sed -i '/\keepalive_timeout.*/a\    \#gzip_http_version 1\.0;' /etc/nginx/nginx.conf
sed -i '/\keepalive_timeout.*/a\    gzip_buffers 4 16k;' /etc/nginx/nginx.conf
sed -i '/\keepalive_timeout.*/a\    gzip_min_length 2k;' /etc/nginx/nginx.conf
sed -i '/\keepalive_timeout.*/a\    gzip  on;' /etc/nginx/nginx.conf
sed -i '/\keepalive_timeout.*/a\ ' /etc/nginx/nginx.conf
sed -i 's/keepalive_timeout.*$/keepalive_timeout 30;/g' /etc/nginx/nginx.conf
```
```
sed -i '/location \/ {.*/i\        large_client_header_buffers 4 128k;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\        client_max_body_size 60m;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\        client_body_buffer_size 512k;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\        fastcgi_connect_timeout 30;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\        fastcgi_read_timeout 30;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\        fastcgi_send_timeout 30;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\        fastcgi_buffer_size 512k;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\        fastcgi_buffers   4 256k;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\        fastcgi_busy_buffers_size 512k;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\        fastcgi_temp_file_write_size 512k;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\ ' /etc/nginx/nginx.conf
```
```
sed -i '/location \/ {.*/{n;d}' /etc/nginx/nginx.conf
#sed -i '/location \/ {.*/{n;d}' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/a\        }' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/a\            index  index.html index.htm index.php;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/a\            root   \/data\/www\/html;' /etc/nginx/nginx.conf
```
```
sed -i '/location \/ {.*/i\ ' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\        location ~ \\\.php\$ {' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\            root           \/data\/www\/html;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\            fastcgi_pass   127\.0\.0\.1\:9000;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\            fastcgi_index  index\.php;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\            fastcgi_param  SCRIPT_FILENAME  \$document_root\$fastcgi_script_name;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\            include        fastcgi_params;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\        }' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\ ' /etc/nginx/nginx.conf
```
```
#过滤到.svn文件
sed -i '/location \/ {.*/i\ ' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\        location ~ ^(\.\*)\\\/\\\.svn\\\/\ {' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\            return 404;' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\        }' /etc/nginx/nginx.conf
sed -i '/location \/ {.*/i\ ' /etc/nginx/nginx.conf
```
### 启动
```
systemctl enable nginx.service
systemctl restart nginx.service
```

## -------------安装git--------------------------------
```
#yum -y install git
```
## -------------压力测试 ab 安装----------------------------
```
yum -y install httpd-tools.x86_64
```

### -----------------------------------------------------
### 编辑自启动
```
#vi /etc/rc.local

```

################################################################
################################################################

## -----------sshd暴力破解的预防--------------
```
#http://www.linuxde.net/2011/09/865.html

#! /bin/bash

cat /var/log/secure|awk '/Failed/{print $(NF-3)}'|sort|uniq -c|awk '{print $2"="$1;}' > /root/black.txt

DEFINE="5"

for i in `cat /root/black.txt`
do

IP=`echo $i |awk -F= '{print $1}'`
NUM=`echo $i|awk -F= '{print $2}'`

if [ $NUM -gt $DEFINE ]; then
grep $IP /etc/hosts.deny > /dev/null

if [ $? -gt 0 ]; then
echo "sshd:$IP" >> /etc/hosts.deny
fi
fi
done
```

## -------------支持中文------------
```
echo "export LANG=zh_CN.UTF-8" >> /home/sshuser/.bash_profile
```
## ------------svn cleanup报错的时候解决
```
#http://blog.csdn.net/luojian520025/article/details/22196865
```
## -------------------阿里云磁盘挂载
```
#http://www.cnblogs.com/dudu/archive/2012/12/07/aliyun-linux-fdisk.html
```
## ----------nginx 配置 ssl 参考----------------
```
#http://www.cnblogs.com/yanghuahui/archive/2012/06/25/2561568.html
openssl genrsa -des3 -out server.key 1024
#填写
openssl req -new -key server.key -out server.csr
#填写
cp server.key server.key.org
openssl rsa -in server.key.org -out server.key
#填写
openssl x509 -req -days 365 -in server.csr -signkey server.key -out server.crt
```

## ---------------DDOS 等网路攻击的了解-----------------------
```
# chattr -i /usr/local/ddos/ignore.ip.list #解除不允许修改
# chattr +i /usr/local/ddos/ignore.ip.list #不允许修改文件
#http://czmmiao.iteye.com/blog/1616837
# ignore ip 问题 http://blog.chinaunix.net/uid-10449864-id-3300661.html
#http://www.77169.org/netadmin/HTML/20150918101159.shtm
#http://www.cnblogs.com/wjoyxt/p/6155672.html
#另：使用CDN分担静态网络内容；使用负载均衡隐藏真实应用ip
```
## -----------------升级 gcc-----------------
```
#http://blog.gimhoy.com/archives/yum-install-gcc4-8-x.html
```
## -----------------vim乱码问题---------------------
```
#http://blog.csdn.net/theblackbeard/article/details/52314974
sed -i '1s/^/set encoding=prc\n/' /etc/vimrc
sed -i '1s/^/set fileformats=unix\n/' /etc/vimrc
sed -i '1s/^/set termencoding=utf-8\n/' /etc/vimrc
sed -i '1s/^/set fileencodings=utf-8,gb2312,gbk,gb18030\n/' /etc/vimrc
sed -i '1s/^/set encoding=prc\n/' /etc/virc
sed -i '1s/^/set fileformats=unix\n/' /etc/virc
sed -i '1s/^/set termencoding=utf-8\n/' /etc/virc
sed -i '1s/^/set fileencodings=utf-8,gb2312,gbk,gb18030\n/' /etc/virc
```
## ---------------------腾讯云挂在硬盘--------------------------
```
#http://jingyan.baidu.com/article/48b37f8d393e9c1a65648847.html
```
## -----------------监控带宽-----------------
```
# http://bbs.qcloud.com/thread-20893-1-1.html
yum install iftop -y
```
## -------------------------------
```
# vi 编辑器的 encoding 的问题，进入 vi 后按 ESC 和 : 键，输入 :set fileencoding=prc
```

## ----------------shadowsocks 代理------------
```
#http://www.hibenben.com/4515.html
```

## -------- ipv6----------------------- 
```
#http://blog.csdn.net/wanglixin1999/article/details/52182001
```