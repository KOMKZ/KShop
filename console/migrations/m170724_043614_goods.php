<?php

use yii\db\Migration;
use common\models\goods\ar\Goods;


class m170724_043614_goods extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, Goods::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `g_id` int(10) unsigned not null auto_increment comment '主键',
            `g_code` char(10) not null comment '商品编号',
            `g_primary_name` varchar(255) not null comment '商品主要名称',
            `g_secondary_name` varchar(255) null comment '商品第二名称',
            `g_cls_id` int(10) unsigned not null comment '商品分类id',
            `g_status` char(15) not null comment '商品状态',
            `g_create_uid` int(10) unsigned not null comment '创建用户id',
            `g_update_uid` int(10) unsigned null default 0 comment '最后更新用户id',
            `g_created_at` int(10) unsigned not null comment '商品创建时间',
            `g_updated_at` int(10) unsigned not null comment '商品创建时间',
            `g_start_at` int(10) unsigned null default 0 comment '商品上架时间',
            `g_end_at` int(10) unsigned  null default 0 comment '商品结束时间',
            primary key `g_id` (g_id),
            index (`g_code`)
        );
        ";
        $this->execute($createTabelSql);
        return true;
    }
    public function safeDown(){
        $tableName = $this->getTableName();
        $dropTableSql = "drop table if exists {$tableName}";
        $this->execute($dropTableSql);
        return true;
    }
}
