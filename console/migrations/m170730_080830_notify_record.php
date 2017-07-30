<?php

use yii\db\Migration;

class m170730_080830_notify_record extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, "{{%notify_result}}"));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `nrt_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
            `nrt_nid` int(10) unsigned NOT NULL COMMENT '通知id',
            `nrt_result` text COMMENT '通知结果响应内容',
            `nrt_created_time` int(10) unsigned NOT NULL COMMENT '主键',
            PRIMARY KEY (`nrt_id`),
            KEY `nrt_nid` (`nrt_nid`)
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
