<?php

use yii\db\Migration;
use common\models\order\ar\OrderDiscount;

class m170906_133835_order_discount_data extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, OrderDiscount::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        CREATE TABLE `{$tableName}` (
          `od_discount_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
          `od_id` int(10) unsigned not null comment '订单id',
          `od_discount_type` varchar(255) not null comment '折扣类型',
          `od_discount_data_id` varchar(255) not null comment '折扣唯一id',
          `od_discount_class` varchar(255) not null comment '折扣所属类',
          `od_discount_data` text not null comment '折扣相关数据',
          `od_discount_description` varchar(255) not null comment '折扣说明',
          `od_discount_slice_value` int(10) unsigned not null default 0 comment '减少的费用',
          `od_discount_created_at` int(10) unsigned not null comment '创建时间',
          primary key (od_discount_id)
        ) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COMMENT='订单折扣数据'
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
