<?php

use yii\db\Migration;
use common\models\user\ar\UserMessage;

class m170812_070332_user_message extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, UserMessage::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `um_id` int(10) unsigned not null auto_increment comment '主键',
            `u_id` int(10) unsigned not null  comment '消息所属用户',
            `um_msg_id` int(10) unsigned null default 0 comment '消息主体id',
            `um_status` char(10) not null comment '消息状态',
            `um_from_uid` int(10) unsigned not null default 0 comment '来源用户',
            `um_type` char(12) not null comment '消息的类型',
            `um_content_type` char(12) not null comment '消息内容的类型',
            `um_content` text not null comment '消息的内容',
            `um_created_at` int(10) unsigned not null comment '创建时间',
            primary key (um_id)
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
