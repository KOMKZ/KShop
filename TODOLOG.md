# TODO

## Prodcut

* 商品资源应该能够排序
* 商品资源应该支持业务分类
* sku属性的约束关系可以考虑写成工具函数
* sku_value 应该比较友好对于seo 1-4:3;5:1 这样一个sku_value 应该直接为14351


## Email

* 实现定时邮件
* 附件过大的邮件将导致邮件队列的worker一直被占用,从而导致其他轻邮件无法及时发送.


## User

* 继续实现其他的httpauth
* 将工厂方法分散到类中来实现

## User's Action Log

* 实现各个操作的行为日志

* 实现db数据层面的监控
  首先BaseActiveRecord实现了curd事件的封装,也就是说行为日志模型只要绑定到这些事件上,就能获取当时操作的ar对象.

  约定所有的想要想实现行为记录的数据库操作,需要实现约定封装,如:

  该动作发生时必须明确指定该动作的内容,如果发现动作内容存在则可以.

* 使用异步的方式来实现记录的写入

  由于大量的行为需要被记录,可能影响系统的性能,故操作写入需要通过异步处理.所有行为记录入队列进行写入操作.

* 考虑使用behaviour的特性来实现功能,参考以下实现

  https://github.com/wenmangayao/yii2-data-log/blob/master/behaviors/LogBehavior.php

## Console

* 解析规则应该重新封装y2log

## 第三方接口

### 第三方转发

* 实现QQ,微信,微博

### 第三方登录

* 实现QQ, 微信, 微薄

### 第三方支付

* 第三方企业付款功能

### 第三方物流

* 顺丰,中通,圆通等

## Order

* 实现订单逻辑

## Promotion

* 实现不同促销规则绑定到不同实体上

## Social Sharing

* 实现大部分第三方分享功能

## Shopping Cart

* 实现购物车功能

## Notification

* 实现通知记录和通知结果的记录
* 实现不同的通知方式

## Message Notification

* 需要实现站内消息,短信,邮件的通知  https://github.com/tuyakhov/yii2-notifications

## Transaction

* 实现站内统一交易应对第三方交易
* 实现站内交易分发通知到不同订单模块

## Setting

* 参考 https://github.com/yii2mod/yii2-settings 并实现

## File

开发特性及功能如下：

* 分片上传的时候应该先来问问这个分片是否存在
* 实现获取文件信息的接口
* 实现查询文件信息的接口
* 将nginx的配置放到服务端
* 公共文件可以使用nginx来做静态文件,同时只是一台单独的服务器

# TOREAD

时间库再封装 http://carbon.nesbot.com/docs/#api-introduction

设置模块 https://github.com/yii2mod/yii2-settings

# Features

## 第三方接口

### 第三方支付

2017-08-18 07:38:24

* 完成微信,支付宝的创建订单,查询订单,查询退款,退款,对账单下载的功能
* ​

## User

2017-08-12 06:46:26

* bearerAuth认证的实现，token的生成使用jwt, 登录时保存jwt的id在user的access_token中，登出时销毁该id。

## Email

2017-08-10 08:19:12

* 实现邮件publish-worker发送逻辑,使用amqp
* 实现邮件model

## Product
2017-08-08 09:16:24

* 实现商品sku属性的制约，参考京东选择商品的sku属性部分属性会变灰的特性
  获取sku的时候会顺便将管理获取到

2017-08-06 14:23:51

* 实现商品资源特性

  商品资源可以属于商品，商品sku，商品选项值。

2017-08-06 14:22:23

* 实现修改商品同时修改关联信息

  已经实现基础，详细，属性，sku的修改

2017-08-02 13:04:43

* 实现商品的meta信息和sku，option信息的分别存储

2017-08-02 08:53:00

* 完成商品基础信息创建
* 完成商品详细信息创建
* 完成商品属性信息创建
* 完成商品sku信息创建


## File

2017-08-06 14:18:50

* 增加一个静态方法用于构造文件的url，不经过查询数据库

  注意能够使用该方法的文件都是公共文件，对私有文件进行使用该方法将无法获取签名信息

2017-07-17 15:02:11

* 实现存储接口，和定义文件操作对象，和返回的数据接口


* 实现本地存储的方法
* 实现oss存储的方法
* 实现本地存储可以通过服务器api直接得到数据流

2017-07-17 15:02:07

* 提交文件时如果提供file_md5_value，实现服务端复制文件的秒传功能

* 修改file/output接口能够输出oss的url

  说明：

  本质上是读取到url，然后跳转过去

2017-07-18 02:32:38

* OSS上传时需要设置好下载名称

2017-07-20 07:57:36

* 实现分片任务的创建

2017-07-22 15:51:25

* 封装分片任务的代码到model层

* 实现分片上传

* 分片任务的任务id的生成方式过于不安全
  改成根据提交的数据过来进行hash，生成文件任务值，只签名部分字段的数据，其中文件md5数据是必须签的。

* 封装方法清楚过期的分片任务
