<?php

use yii\db\Migration;
use common\models\goods\ar\GoodsSource;

class m170806_100616_goods_source extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, GoodsSource::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `gs_id` int(10) unsigned not null auto_increment comment '主键',
            `gs_type` char(12) not null comment '资源类型',
            `gs_name` varchar(60) null default '' comment '资源的名称',
            `gs_sid` varchar(255) not null comment '资源id',
            `gs_cls_type` char(12) not null comment '资源所属分类',
            `gs_cls_id` int(10) unsigned not null comment '资源分类的id',
            `g_id` int(10) unsigned not null comment '商品id',
            `gs_use_type` char(10) not null default '' comment '资源用途分类',
            `gs_created_at` integer(10) unsigned not null comment '',
            primary key `gs_id` (gs_id),
            index (gs_cls_type, gs_cls_id),
            index (gs_cls_type, gs_cls_id, gs_type)
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
