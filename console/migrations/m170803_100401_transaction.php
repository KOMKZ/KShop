<?php

use yii\db\Migration;
use common\models\trans\ar\Transaction;

class m170803_100401_transaction extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, Transaction::tableName()));
    }
    public function safeUp(){
        return true;
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `gm_id` int(10) unsigned not null auto_increment comment '主键',
            `g_id` int(10) unsigned not null comment '商品id',
            `g_atr_id` int(10) unsigned not null comment '商品属性id',
            `gm_value` varchar(100) not null comment '商品元属性的值',
            `gm_status` char(10) not null comment '商品属性状态',
            `gm_created_at` int(10) not null comment '创建时间',
            primary key `gm_id` (gm_id),
            unique `g_id_and_g_atr_id` (g_id, g_atr_id)
        );
        ";
        $this->execute($createTabelSql);
        return true;
    }
    public function safeDown(){
        return true;
        $tableName = $this->getTableName();
        $dropTableSql = "
        drop table if exists {$tableName}
        ";
        $this->execute($dropTableSql);
        return true;
    }
}
