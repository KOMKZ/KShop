<?php

use yii\db\Migration;
use common\models\message\ar\MessageTpl;

class m170812_071748_message_tpl extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, MessageTpl::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `mtpl_id` int(10) unsigned not null auto_increment comment '主键',
            `mtpl_code` varchar(64) not null comment '模板代号',
            `mtpl_content` text not null comment '模板内容',
            `mtpl_created_at` int(10) unsigned not null comment '创建时间',
            primary key (mtpl_id)
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
