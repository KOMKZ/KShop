<?php

use yii\db\Migration;
use common\models\goods\ar\GoodsClassification;

class m170723_042912_goods_classification extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, GoodsClassification::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `g_cls_id` int(10) unsigned not null auto_increment comment '主键',
            `g_cls_name` varchar(64) not null comment '商品分类的名称',
            `g_cls_show_name` varchar(64) null default '' comment '商品分类的展示名称',
            `g_cls_pid` int(10) unsigned not null default 0 comment '商品分类父级分类名称',
            `g_cls_created_at` int(10) unsigned not null comment '商品创建时间',
            primary key `g_cls_id` (g_cls_id)
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
