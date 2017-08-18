<?php

use yii\db\Migration;
use common\models\message\ar\MessageMap;

class m170812_072701_message_map extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, MessageMap::tableName()));
    }

    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `mm_id` int(10) unsigned not null auto_increment comment '主键',
            `mm_type` char(12) not null comment '消息类型',
            `mm_content_type` char(12) not null comment '消息内容的类型',
            `mm_content` text not null comment '消息内容',
            `mm_tpl_code` varchar(64) not null default '' comment '消息模板',
            `mm_create_uid` int(10) unsigned not null comment '创建者uid',
            `mm_vars` text null comment '模板变量参数',
            `mm_receipt_uid` int(10) unsigned not null default 0 comment '接受人uid',
            `mm_created_time` int(10) unsigned not null comment '创建时间',
            primary key (mm_id)
        )CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB;
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
