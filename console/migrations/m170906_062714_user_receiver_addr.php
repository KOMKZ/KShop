<?php

use yii\db\Migration;
use common\models\user\ar\UserReceiverAddr;

class m170906_062714_user_receiver_addr extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, UserReceiverAddr::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        CREATE TABLE `{$tableName}` (
          `rece_addr_id` int(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
          `rece_name` varchar(64) not null comment '收货人姓名',
          `rece_status` char(12) not null comment '条目状态',
          `rece_contact_number` varchar(255) not null comment '收货人手机号',
          `rece_location_id` varchar(255) not null comment '收货人地区id',
          `rece_location_string` varchar(255) not null comment '收货人详细地址',
          `rece_tag` varchar(255) not null default '' comment '标签,关键词',
          `rece_default_addr` char(3) not null comment '是否是默认地址',
          `rece_belong_uid` int(10) unsigned not null comment '所属用户id',
          `rece_created_at` int(10) unsigned not null comment '创建时间',
          primary key (rece_addr_id)
        ) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COMMENT='用户收货地址'
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
