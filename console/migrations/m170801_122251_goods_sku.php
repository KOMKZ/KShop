<?php

use yii\db\Migration;
use common\models\goods\ar\GoodsSku;

class m170801_122251_goods_sku extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, GoodsSku::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `g_sku_id` bigint(20) unsigned not null auto_increment comment '主键',
            `g_sku_name` varchar(255) null default '' comment '商品sku名称',
            `g_sku_value_name` varchar(255) not null comment '商品sku值label',
            `g_id` int(10) unsigned not null comment '商品id',
            `g_sku_value` varchar(255) not null comment 'sku值',
            `g_sku_stock_num` int(10) not null comment '库存量',
            `g_sku_price` int(10) not null comment 'sku实体实际价格',
            `g_sku_sale_price` int(10) not null comment 'sku实体销售价格',
            `g_sku_status` char(16) not null comment 'sku状态',
            `g_sku_created_at` int(10) not null comment 'sku创建时间',
            `g_sku_updated_at` int(10) null default 0 comment 'sku更新时间',
            `g_sku_create_uid` int(10) not null comment 'sku创建者',
            `g_sku_update_uid` int(10) null default 0 comment 'sku更新者',
            primary key `g_sku_id` (g_sku_id)
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
