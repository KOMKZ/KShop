<?php

use yii\db\Migration;
use common\models\order\ar\OrderGoods;

class m170829_144745_order_goods extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, OrderGoods::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        CREATE TABLE `{$tableName}` (
          `og_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单商品主键',
          `od_id` int(10) unsigned not null comment '订单主键',
          `g_id` int(10) unsigned not null comment '商品主键',
          `g_sku_value` varchar(255) not null comment '商品sku值',
          `g_sku_id` int(10) unsigned not null comment 'sku id',
          `og_g_sku_price` int(10) unsigned not null comment 'sku实体实际价格',
          `og_g_sku_total_price` int(10) unsigned not null comment '多个sku最终价格',
          `og_g_bug_number` int(10) unsigned not null default 1 comment '购买数量',
          `og_g_sku_sale_price` int(10) not null comment 'sku销售价格',
          `og_g_sku_name` varchar(255) null default '' comment '商品sku名称',
          `og_g_sku_value_name` varchar(255) not null comment '商品sku值label',
          `og_g_sku_data` text null comment '商品sku当时数据',
          `og_discount_data` text null comment '订单sku折扣和优惠数据',
          `og_created_at` int(10) unsigned not null comment '创建时间',
          `og_updated_at` int(10) unsigned not null comment '更新时间',
          primary key (og_id)
        ) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COMMENT='订单表'
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
