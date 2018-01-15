<?php

use yii\db\Migration;
use common\models\sms\ar\Sms;

class m180115_124219_sms extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, Sms::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `sms_id` int(10) unsigned not null auto_increment comment '主键',
            `sms_provider` varchar(15) not null comment '短信服务商',
            `sms_type` char(10) not null comment '短信类型',
            `sms_inner_code` varchar(64) not null comment '短信内部编号',
            `sms_outer_code` varchar(64) not null comment '短信外部编号，一般是模板编号',
            `sms_to_uid` int(10) unsigned not null default 0 comment '接受用户id',
            `sms_to_phone` varchar(18) not null comment '短信接收手机号码',
            `sms_params` text comment '相关参数',
            `sms_created_at` int(10) unsigned not null comment '创建时间',
            primary key (`sms_id`)
        );
        ";
        $this->execute($createTabelSql);
        return true;
    }
    public function safeDown(){
        $tableName = $this->getTableName();
        $dropTableSql = "
        drop table if exists {$tableName}
        ";
        $this->execute($dropTableSql);
        return true;
    }
}
