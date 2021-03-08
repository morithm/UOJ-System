<p align="center"><img src="https://github.com/UniversalOJ/UOJ-System/blob/master/web/images/logo.png?raw=true"></p>

# Universal Online Judge

> #### 一款通用的在线评测系统。

## 安装

```bash
sudo docker run --name uoj -dit -p 80:80 -p 3690:3690 --cap-add SYS_PTRACE universaloj/uoj-system
```

## 版本更新

### 方法一

包含数据的三个文件夹： ``mysql``，``uoj_data``，``storage`` 。

把三个文件夹复制出来：

```bash
sudo docker cp uoj:/var/lib/mysql .
sudo docker cp uoj:/var/uoj_data .
sudo docker cp uoj:/var/www/uoj/app/storage .
```

配置文件：``/opt/uoj/judger/.conf.json``，``/opt/uoj/web/app/.config.php``

复制出来

```bash
sudo docker cp uoj:/opt/uoj/judger/.conf.json .
sudo docker cp uoj:/opt/uoj/web/app/.config.php .
```

然后更新``image``

```bash
sudo docker pull universaloj/uoj-system
```

删除旧的 ``container`` 并重新安装，利用 ``-v`` 映射一下文件夹

```bash
sudo docker stop uoj
sudo docker rm uoj
sudo docker run --name uoj -dit -p 80:80 -p 3690:3690 -v $PWD/mysql:/var/lib/mysql -v $PWD/uoj_data:/var/uoj_data -v $PWD/storage:/var/www/uoj/app/storage --cap-add SYS_PTRACE universaloj/uoj-system
```

粘贴配置文件：

```bash
sudo docker cp .conf.json uoj:/opt/uoj/judger/.conf.json
sudo docker cp .config.php uoj:/opt/uoj/web/app/.config.php
```

因为几个文件夹是映射的，所以要重新 ``chown`` 一下(如果 ``Dockerfile``配了 ``VOLUME``) 就不用
```bash
chown -R www-data /var/www/uoj/app/storage
chown -R www-data:www-data /var/uoj_data
chown -R local_main_judger:local_main_judger /opt/uoj/judger
su local_main_judger -c '/opt/uoj/judger/judge_client start'
```

### 方法二

上面这种方式比较麻烦，可以直接暴力一点把 ``/opt/uoj`` 用``volume``映射，然后 ``git pull`` 更新版本

#### 准备工作

```bash
sudo docker cp uoj:/var/lib/mysql .
sudo docker cp uoj:/opt/uoj .
sudo docker stop uoj
sudo docker rm uoj
sudo docker run --name uoj -dit -p 80:80 -p 3690:3690 -v $PWD/mysql:/var/lib/mysql -v $PWD/uoj:/opt/uoj -v $PWD/uoj_data:/var/uoj_data --cap-add SYS_PTRACE universaloj/uoj-system
```

#### 更新

```bash
cd uoj
# 这里注意 .gitignore 配置一下 config 数据这些
git pull origin
```

更新完记得 ``chown`` 一下

## 特性

- 前后端全面更新为 Bootstrap 4 + PHP 7。
- 多种部署方式，各取所需、省心省力、方便快捷。
- 各组成部分可单点部署，也可分离部署；支持添加多个评测机。
- 题目搜索，全局放置，任意页面均可快速到达。
- 所有题目从编译、运行到评分，都可以由出题人自定义。
- 引入 Extra Tests 和 Hack 机制，更加严谨、更有乐趣。
- 支持 OI/IOI/ACM 等比赛模式；比赛内设有提问区域。
- 博客功能，不仅可撰写图文内容，也可制作幻灯片。

## 文档

有关安装、管理、维护，可参阅：[https://universaloj.github.io/](https://universaloj.github.io/)

## 感谢

- [vfleaking](https://github.com/vfleaking) 将 UOJ 代码[开源](https://github.com/vfleaking/uoj)
- 向原项目或本项目贡献代码的人
- 给我们启发与灵感以及提供意见和建议的人

