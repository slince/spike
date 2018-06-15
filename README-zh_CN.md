<p align="center">
    <img src="https://raw.githubusercontent.com/slince/spike/master/resources/logo.png"/>
</p>

<p align="center">
    <a href="LICENSE" target="_blank">
        <img alt="Software License" src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square">
    </a>
    <a href="https://travis-ci.org/slince/spike">
        <img src="https://img.shields.io/travis/slince/spike/master.svg?style=flat-square" alt="Build Status">
    </a>
    <a href="https://codecov.io/github/slince/spike">
        <img src="https://img.shields.io/codecov/c/github/slince/spike.svg?style=flat-square" alt="Coverage Status">
    </a>
    <a href="https://packagist.org/packages/slince/spike">
        <img src="https://img.shields.io/packagist/v/slince/spike.svg?style=flat-square&amp;label=stable" alt="Latest Stable Version">
    </a>
    <a href="https://scrutinizer-ci.com/g/slince/spike/?branch=master">
        <img src="https://img.shields.io/scrutinizer/g/slince/spike.svg?style=flat-square" alt="Scrutinizer">
    </a>
</p>

Spike是一个可以用来将你的内网服务暴露在公网的快速的反向代理，基于[ReactPHP](https://github.com/reactphp)，采用IO多路复用模型。

## 安装

通过 composer 安装

```bash
composer global require slince/spike
```

> 服务器与本地都需要执行此命令安装


## 原理图

<p align="center">
    <img src="https://raw.githubusercontent.com/slince/spike/master/resources/diagram.png"/>
</p>

## 配置服务端

使用Spike的前提是你需要有一台公网可访问的机器，这里假设你已经有一台机器.你可以使用下面两种方式开启服务端。

### 基于默认参数

执行下面命令以开启服务

```bash
$ spiked --address=127.0.0.1:8088
```
上述命令可以创建一个基本服务，如果你需要定制更多信息可以基于配置文件服务


### 基于配置文件

- 初始化一个配置文件 

执行下面命令创建文件

```bash
$ spiked init --dir=/home/conf --format=json
```

支持yaml,xml,ini,json四种格式的配置文件，默认使用json，使用下面命令查看帮助

```bash
$ spiked init -h
```

- 打开配置文件，修改相关参数

- 基于配置文件开启服务
 
```bash
 $ spiked --config=/home/conf/spiked.json
```

## 配置本地客户端

开启客户端需要先创建配置文件

- 初始化一个配置文件 

执行下面命令创建文件

```bash
$ spike init --dir=/home/conf --format=json
```

使用下面命令查看帮助

```bash
$ spike init -h
```

- 打开配置文件，修改相关参数

- 基于配置文件开启服务
 
```bash
$ spike --config=/home/conf/spike.json
```


## 定义隧道

隧道的定义只在客户端，服务端不需要做任何配置，从而达到最简化配置。

> 目前支持http与tcp两种隧道

打开本地配置文件"spike.json", 修改tunnel一项;

- 添加http隧道

```json
{
    "protocol": "http",
    "serverPort": 8086,
    "proxyHosts": {
        "www.foo.com": "127.0.0.1:80",
        "www.bar.com": "192.168.1.101:8080"
    }
}
```
启动客户端，访问 "http://www.foo.com:8086" , 服务将会被代理到本地"127.0.0.1:80"; 注意此处需要把 "www.foo.com" 解析到服务端所在机器上

- 添加tcp隧道

基于tcp协议的应用层协议都可使用本隧道代理，如：mysql,redis,ssh...等；下面是代理mysql服务的例子

```json
{
    "protocol": "tcp",
    "serverPort": 8087,
    "host": "127.0.0.1:3306"
}
```
执行下面命令访问本地mysql服务：

```bash
$ mysql -h 服务器地址 -P 8087
```

## 客户端身份认证

基于默认参数开启的服务端没有开启客户端身份认证服务，如果需要开启该服务则只能基于配置文件去启动服务端. 

- 服务端启用认证服务

打开"spiked.json"文件，修改auth一项信息，然后重启服务

> 目前只支持简单的用户名密码认证方式，更多的认证方式后面会陆续加入.

- 修改客户端身份信息

打开本地"spike.json"文件，修改auth一栏信息，与服务端配置保持一致即可


## 日志配置

默认开启屏幕输出与文件两种形式的日志；前者会打印到控制台；后者会写入到指定文件；默认日志等级是"info"，此项信息可以通过
修改配置文件"log"一项调整；

## 查看所有命令

```bash
$ spike list
   _____   _____   _   _   _    _____
  /  ___/ |  _  \ | | | | / /  | ____|
  | |___  | |_| | | | | |/ /   | |__
  \___  \ |  ___/ | | | |\ \   |  __|
   ___| | | |     | | | | \ \  | |___
  /_____/ |_|     |_| |_|  \_\ |_____|
  
  Spike Client 0.0.1
  
  Usage:
    command [options] [arguments]
  
  Options:
    -h, --help            Display this help message
    -q, --quiet           Do not output any message
    -V, --version         Display this application version
        --ansi            Force ANSI output
        --no-ansi         Disable ANSI output
    -n, --no-interaction  Do not ask any interactive question
    -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
  
  Available commands:
    help        Displays help for a command
    init        Create a configuration file in the specified directory
    list        Lists commands
    list-proxy  Lists all supported proxy hosts by the client
```

## Changelog

查看 [CHANGELOG.md](./CHANGELOG.md)

## License
 
The MIT license. See [MIT](https://opensource.org/licenses/MIT)