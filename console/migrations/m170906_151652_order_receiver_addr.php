<?php

use yii\db\Migration;
use common\models\order\ar\OrderReceiverAddr;

class m170906_151652_order_receiver_addr extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, OrderReceiverAddr::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        CREATE TABLE `{$tableName}` (
          `od_receaddr_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
          `od_id` int(10) unsigned not null comment '订单id',
          `rece_addr_id` int(10) unsigned NOT NULL COMMENT '参考收货主键',
          `rece_name` varchar(64) not null comment '收货人姓名',
          `rece_contact_number` varchar(255) not null comment '收货人手机号',
          `rece_location_id` varchar(255) not null comment '收货人地区id',
          `rece_location_string` varchar(255) not null comment '收货人详细地址',
          `rece_full_addr` varchar(255) not null comment '收货人完整收货地址',
          `rece_tag` varchar(255) not null default '' comment '标签,关键词',
          `rece_belong_uid` int(10) unsigned not null comment '所属用户id',
          primary key (od_receaddr_id)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单折扣数据'
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
