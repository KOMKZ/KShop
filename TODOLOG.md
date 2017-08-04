# TODO

## Prodcut

* 实现修改商品同时修改关联信息

## Email

* 实现邮件model
* 实现邮件publish-worker发送逻辑,使用amqp

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

## Order

* 实现订单逻辑

## Promotion

* 实现不同促销规则绑定到不同实体上

## Social Sharing

* 实现大部分第三方分享功能


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

# TOREAD

时间库再封装 http://carbon.nesbot.com/docs/#api-introduction

设置模块 https://github.com/yii2mod/yii2-settings

# Features

## Product
2017-08-02 13:04:43

* 实现商品的meta信息和sku，option信息的分别存储

2017-08-02 08:53:00

* 完成商品基础信息创建
* 完成商品详细信息创建
* 完成商品属性信息创建
* 完成商品sku信息创建


## File

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
