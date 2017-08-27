<?php

use yii\db\Migration;
use common\models\order\ar\CartItem;

class m170827_055357_cart_item extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, CartItem::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        CREATE TABLE `{$tableName}` (
            `ct_id` int(10) unsigned not null AUTO_INCREMENT COMMENT '购物车项目id',
            `ct_belong_uid` int(10) unsigned not null comment '所属用户',
            `ct_object_id` int(10) unsigned not null comment '购物车条目主id',
            `ct_object_value` varchar(255) null default '' '购物车条目业务id',
            `ct_object_type` char(12) not null comment '条目的类型',
            `ct_object_data` text null comment '条目该时刻的数据',
            `ct_discount_data` text null comment '折扣及优惠数据选择',
            `ct_object_classification` char(20) not null comment '对象的分类',
            `ct_object_status` char(12) not null comment '条目的状态',
            `ct_amount` int(10) unsigned not null comment '该条目的数量',
            `ct_price` int(10) unsigned not null comment '该时刻的条目价格',
            `ct_price_type` char(10) not null comment '价格类型',
            `ct_object_title` varchar(255) not null comment '条目该时刻的标题',
            `ct_created_at` int(10) unsigned not null comment '创建时间',
            primary key (ct_id)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='购物车项目'
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
