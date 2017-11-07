<?php

Laravel Homestead#
简介#
//Laravel Homestead 是一个官方预封装的 Vagrant box，它为你提供了一个完美的开发环境，你无需在本地安装 PHP 、web 服务器或任何服务软件。 Vagrant box 是完全一次性的，不用担心会搞乱你的操作系统！如果有什么地方出错了，你可以在几分钟内销毁并重建该 box ！
Homestead 可以在任何 Windows、Mac 或 Linux 系统上运行，它包括了 Nginx Web 服务器、PHP 7.1、MySQL、PostgresSQL、Redis、Memcached、Node 以及开发 laravel 应用所需的东西
!!如果你使用的是 Windows，你可能需要通过 BIOS 来启用硬件虚拟化（VT-x）。如果你在 UEFI 系统上使用 Hyper-V，可能还需要禁用 Hyper-V 才能启用 VT-x。

内置软件#
	Ubuntu 16.04
	Git
	PHP 7.1
	Nginx
	MySQL
	MariaDB
	Sqlite3
	Postgres
	Composer
	Node (带有 Yarn、Bower、Grunt 和 Gulp)
	Redis
	Memcached
	Beanstalkd
	Mailhog
	ngrok

安装与设置#
第一步#
//在启动 Homestead 环境之前，你必须先安装 VirtualBox 5.1／VMWare／Parallels 以及 Vagrant。上述软件均针对不同操作系统提供了易于使用的可视化安装包。

安装 Homestead Vagrant 盒子#
//当安装完 VirtualBox／VMware 以及 Vagrant 后，你可以在终端使用下面的命令将 laravel/homestead 盒子添加到 Vagrant 中安装。下载盒子需要几分钟的时间，具体取决于你的互联网连接速度
vagrant box add laravel/homestead

安装 Homestead#
//建议将代码库克隆到用户「home」目录下的 Homestead 文件夹中。这样 Homestead 盒子就可以作为所有 Laravel 项目的主机
cd ~
git clone https://github.com/laravel/homestead.git Homestead

//由于 Homestead 的 master 分支并不是稳定分支，你应该用打过标签的稳定版本。你可以在 Github 发行页面 上找到最新的稳定版本。
cd Homestead

// Clone the desired release...
git checkout v6.1.0

//克隆 Homestead 代码库后，从 Homestead 目录中运行 bash init.sh 命令来创建 Homesstead.yaml 配置文件。 Homesstead.yaml 文件会被放置在你的 Homestead 目录中：
// Mac / Linux...
bash init.sh

// Windows...
init.bat

配置 Homestead#
配置提供器##
//Homestead.yaml 中的 provider 参数设置决定了你用的是哪一个 Vagrant 提供器：virtualbox、vmware_fusion、vmware_workstation 或者 parallels。你可以根据自己的喜好来设置提供器：
provider: virtualbox

配置共享文件夹##
//Homestead.yaml 文件的 folders 属性里列出所有与 Homestead 环境共享的文件夹。这些文件夹中的文件若有变更，它们会保持本地机器与 Homestead 环境之间同步。你可以根据需要配置多个共享文件夹：
folders:
    - map: ~/Code
      to: /home/vagrant/Code

//若要启动 NFS，只需要在共享的文件夹配置中添加一个简单的标志：
folders:
    - map: ~/Code
      to: /home/vagrant/Code
      type: "nfs"

!!使用 NFS 时，建议你安装 vagrant-bindfs 插件。这个插件会替你处理 Homestead 盒子中的文件或目录权限问题。

//你也可以通过在 options 下方列出 Vagrant 的 共享文件夹 支持的任何选项：
folders:
    - map: ~/Code
      to: /home/vagrant/Code
      type: "rsync"
      options:
          rsync__args: ["--verbose", "--archive", "--delete", "-zz"]
          rsync__exclude: ["node_modules"]

配置 Nginx 站点##
//对 Nginx 不熟悉吗？没关系。sites 属性可以帮助你可以轻松地将 域名 映射到 homestead 环境中的文件夹。Homestead.yaml 文件中已包含示例站点配置
//同样的，你也可以增加多个站点到你的 Homestead 环境中。 Homestead 可以同时为多个 Laravel 应用提供虚拟化环境：
sites:
    - map: homestead.app
      to: /home/vagrant/Code/Laravel/public

//如果你在配置 Homestead 盒子之后更改了 sites 属性，那么应该重新运行 vagrant reload --provision 来更新虚拟机上的 Nginx 配置。
vagrant reload --provision

关于 Hosts 文件##
//你必须将在 Nginx 站点中所添加的「域名」也添加到你机器的 hosts 上。 hosts 文件会将 Homestead 站点的请求重定向到 Homestead 盒子中。在 Mac 或 Linux 上，该文件位于 /etc/hosts。在 Windows 上，它位于 C:\Windows\System32\drivers\etc\hosts。添加的内容如下所示
192.168.10.10  homestead.app

//请确保列出的 IP 地址是你 Homestead.yaml 文件中的 IP 地址。将域名设置到 hosts 文件并启动 Vagrant 盒子后，你就可以通过 Web 浏览器访问该站点：
http://homestead.app

启动 Vagrant 盒子#
vagrant up
//Vagrant 将启动虚拟机并自动配置你的共享文件夹和 Nginx 站点。
//如果要删除虚拟机，使用 vagrant destroy --force 命令。



根据项目安装#
//除了全局安装 Homestead 并且在所有项目共享相同的 Homestead 盒子外，你可以为每个项目配置 Homestead 实例。通过在项目下创建 Vagrantfile，可以实现为每个项目分别安装上 Homestead ，其他项目成员只需要简单地运行 vagrant up 就能都拥有同样的开发环境。
要将 Homestead 直接安装到项目中，需要使用 Composer
composer require laravel/homestead --dev

//Homestead 安装完后，可以使用 make 命令在项目根目录中生成 Vagrantfile 与 Homestead.yaml 文件
make 命令会自动配置 Homestead.yaml 文件中的 sites 及 folders 指令。

Mac/Linux:
--php vendor/bin/homestead make

Windows:
--vendor\\bin\\homestead make

//接下来，在终端中运行 vagrant up 并在浏览器中访问你的项目 http://homestead.app。再次提醒：你仍然需要在 /etc/hosts 里配置 homestead.app 或其它想要使用的域名。

安装 MariaDB#
//如果你喜欢使用 MariaDB 而不是 MySQL，你可以在 Homestead.yaml 文件中增加一个 mariadb 的选项。这个选项会删除 MySQL 并安装 MariaDB。MariaDB 只是作为 MySQL 的替代品，因此你还是可以在应用的数据库配置中使用 mysql 数据库驱动：
	box: laravel/homestead
	ip: "192.168.20.20"
	memory: 2048
	cpus: 4
	provider: virtualbox
	mariadb: true

常见用法#
全局可用的 Homestead#
//你可以在 Mac / Linux 系统的 Bash 配置文件中添加 Bash 函数。在 Windows 中，你可以通过在 PATH 环境变量中添加一个「批处理」文件来实现
//下面这些脚本使你可以从系统的任何地方运行任何 Vagrant 命令，并将自动将该命令指向你的 Homestead 安装路径
Mac / Linux#
function homestead() {
    ( cd ~/Homestead && vagrant $* )
}
//确保将该功能中的 ~/Homestead 路径是你实际的 Homestead 安装路径。这样你就可以在系统的任何地方运行 homestead up 或 homestead ssh 等命令。

Windows#
//在系统的任意位置创建一个批处理文件 homestead.bat ，并添加如下内容：
@echo off

set cwd=%cd%
set homesteadVagrant=C:\Homestead

cd /d %homesteadVagrant% && vagrant %*
cd /d %cwd%

set cwd=
set homesteadVagrant=
//请确保将例子中 C:\Homestead 这个路径修改为你的实际 Homestead 的安装路径。创建文件后，将文件位置添加到环境变量 PATH 中。然后就可以在系统的任意位置运行 homestead up 或 homestead ssh 命令。

通过 SSH 连接#
//你可以通过在 Homestead 目录运行 vagrant ssh 命令来连接虚拟主机。
vagrant ssh
//你可能需要频繁地使用 SSH 连接 Homestead 主机，可以尝试着利用上述「功能」来快速地使用 SSH 连接 Homestead 盒子。


连接数据库#
//在盒子中已经为 MySQL 和 Postgres 配置好了一个数据库 homestead。为了更方便的使用它，Laravel 中的 .env 文件将框架配置成默认使用此数据库。
要从主机的数据库客户端连接到 MySQL 或 Postgres，就连接到 127.0.0.1 和端口 33060 (MySQL) 或 54320 (Postgres)。账号密码分别是 homestead／secret


增加更多网站#
//Homestead 环境配置完毕且成功运行后，你可能会想要为 Laravel 应用程序增加其他的 Nginx 站点。你可以在单个 Homestead 环境中运行多个 Laravel 程序。
//要添加其他网站，只需将网站配置信息添加到 Homestead.yaml 文件中
sites:
    - map: homestead.app
      to: /home/vagrant/Code/Laravel/public
    - map: another.app
      to: /home/vagrant/Code/another/public

//如果 Vagrant 没有自动管理你的「hosts」文件，你可能需要手动把新增的站点加入到该文件中：
192.168.10.10  homestead.app
192.168.10.10  another.app

//添加站点后，从 Homestead 目录运行 vagrant reload --provision 命令就可以应用新的更改
vagrant reload --provision

站点类型#
//Homestead 支持多种类型的站点，可以让你轻松地运行那些不基于 Laravel 的项目。
//例如，我们可以使用 symfony2 站点类型轻松地在 Homestead 中添加 Symfony 应用程序：
sites:
    - map: symfony2.app
      to: /home/vagrant/Code/Symfony/web
      type: symfony2

//支持的站点类型有： apache、laravel（默认）、proxy、silverstripe、statamic、symfony2 和 symfony4。

站点参数#
//你还可以使用 params 站点指令向你的站点添加其他 Nginx fastcgi_param 值。例如，添加一个值为 BAR 的 FOO 参数。
sites:
    - map: homestead.app
      to: /home/vagrant/Code/Laravel/public
      params:
          - key: FOO
            value: BAR


配置 Cron 调度器#
//Laravel 提供了便利的方式来 调度 Cron 任务，通过Artisan 命令 schedule:run ，调度便会在每分钟运行一次。
schedule:run
//schedule:run 命令会检查定义在你 App\Console\Kernel 类中的调度任务，以此判断哪个任务该被运行。
//如果你想对 Homestead 站点使用 schedule:run 命令，你需要在定义站点时将 schedule 选项设置为 true
sites:
    - map: homestead.app
      to: /home/vagrant/Code/Laravel/public
      schedule: true
//该站点的 Cron 任务会被定义在虚拟机的 /etc/cron.d 文件夹中

端口#
//默认情况下，以下端口会被转发至 Homestead 环境：
	SSH: 2222 → 发送到 22
	HTTP: 8000 → 发送到 80
	HTTPS: 44300 → 发送到 443
	MySQL: 33060 → 发送到 3306
	Postgres: 54320 → 发送到 5432
	Mailhog: 8025 → 发送到 8025
转发更多端口#
//你可以根据需要转发更多端口给 Vagrant 盒子，并指定其协议：
ports:
    - send: 50000
      to: 5000
    - send: 7777
      to: 777
      protocol: udp

共享你的环境#
//Vagrant 提供了一个内置方法 vagrant share 来支持。不过，如果你的 Homestead.yaml 文件中配置了多个站点，就无法使用此命令。

//为了解决这个问题，Homestead 提供了自己的 share 命令。开始之前，通过 vagrant ssh SSH 命令连接 Homestead 机器中并运行 share homestead.app。这会从 Homestead.yaml 配置文件中共享 homestead.app 站点。你也可以用其他已经配置的站点来代替 homestead.app。
share homestead.app

//运行命令后，你可以看到一个 Ngrok 界面，其中包含活动日志和共享站点的可公开访问的 URL。如果要指定自定义地区或者其他 Ngrok 选项，可以将它们添加到 share 命令后面：
share homestead.app -region=eu -subdomain=laravel
!!谨记，Vagrant 本质上是不安全的。当你运行 share 命令时，你已经在互联网中暴露了你的虚拟机

多个 PHP 版本#
//Homestead 6 支持在同一个虚拟机上引入多个版本的 PHP。你可以在 Homestead.yaml 文件中为给定站点指定使用哪个版本的 PHP。 可用的 PHP 版本有：「5.6」、「7.0」、「7.1」
sites:
    - map: homestead.app
      to: /home/vagrant/Code/Laravel/public
      php: "5.6"
//此外，你还可以通过 CLI 使用任何受支持的 PHP 版本：
php5.6 artisan list
php7.0 artisan list
php7.1 artisan list

网络接口#
// Homestead.yaml 的 networks 属性为 Homestead 环境配置网络接口。你可以根据需要配置任意数量的接口：
networks:
    - type: "private_network"
      ip: "192.168.10.20"
// 想启用 桥接 接口，请配置 bridge 设置，并将网络类型更改为 public_network ：
networks:
    - type: "public_network"
      ip: "192.168.10.20"
      bridge: "en1: Wi-Fi (AirPort)"

//要启用 DHCP，只需从配置中删除 ip 选项：
networks:
    - type: "public_network"
      bridge: "en1: Wi-Fi (AirPort)"


更新 Homestead#
//你可以通过两个简单的步骤更新 Homestead。首先，使用 vagrant box update 命令更新 Vgrant 盒子:
vagrant box update

//接下来，如果你是通过克隆仓库的方式来安装的 Homestead，你需要更新 Homestead 的源代码。你可以在你最初克隆仓库的位置简单地运行 git pull origin master 命令。
//如果你是通过项目中的 composer.json 文件安装 Homestead ，则应该确认 composer.json 文件中包含 "laravel/homestead: "^6" 并更新依赖：
composer update

历史版本#
!!如果你需要一个旧版本的 PHP，请在尝试使用旧版本的 Homestead 之前，先阅读文档 多个 PHP 版本 上的文档。
//你可以通过添加以下内容添加到 Homestead.yaml 文件中来覆盖 Homestead 使用的盒子版本
version: 0.6.0

例如：
	box: laravel/homestead
	version: 0.6.0
	ip: "192.168.20.20"
	memory: 2048
	cpus: 4
	provider: virtualbox

//当你使用较旧版本的 Homestead 盒子时，你需要确保将其与 Homestead 源代码的兼容版本进行匹配。下面的图表展示了支持的盒子版本，使用哪个版本的 Homestead 源代码以及提供的 PHP 版本


提供器的特殊设置#
VirtualBox#

//Homestead 默认将 natdnshostresolver 设置为 on
//这允许 Homestead 使用主机系统中的 DNS 设置。如果你想重写这行为，你可以在你的 Homestead.yaml 文件中添加下面这几行：
provider: virtualbox
natdnshostresolver: off