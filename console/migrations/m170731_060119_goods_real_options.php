<?php

use yii\db\Migration;
use common\models\goods\ar\GoodsRealOption;


class m170731_060119_goods_real_options extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, GoodsRealOption::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `g_opt_id` int(10) unsigned not null auto_increment comment '主键',
            `g_opt_value` smallint(3) unsigned not null comment '选项值',
            `g_opt_status` char(10) not null comment '状态',
            `g_id` int(10) unsigned not null comment '商品id',
            `g_atr_id` int(10) unsigned not null comment '商品属性id',
            `g_opt_name` varchar(100) not null comment '商品属性选项值',
            `g_opt_img` varchar(255) null comment '商品属性选项值图片',
            `g_opt_created_at` int(10) not null comment '创建时间',
            primary key `g_opt_id` (g_opt_id)
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
