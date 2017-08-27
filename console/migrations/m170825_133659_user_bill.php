<?php

use yii\db\Migration;
use common\models\user\ar\UserBillRecord;

class m170825_133659_user_bill extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, UserBillRecord::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        CREATE TABLE `{$tableName}` (
            `u_bill_id` int(10) unsigned not null AUTO_INCREMENT COMMENT '用户账单数据主键',
            `u_id` int(10) unsigned not null COMMENT '关联用户id',
            `u_bill_type` char(10) not null comment '变更记录类型',
            `u_bill_fee` int(10) unsigned not null comment '该次的费用',
            `u_bill_fee_type` char(10) not null comment '货币类型',
            `u_bill_related_id` char(20) not null comment '相关操作对象id',
            `u_bill_related_type` char(10) not null comment '相关操作对象类型',
            `u_bill_trade_no` char(20) not null comment '交易号',
            `u_bill_created_at` int(10) unsigned not null comment '创建时间',
            primary key (`u_bill_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户账单数据'
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
