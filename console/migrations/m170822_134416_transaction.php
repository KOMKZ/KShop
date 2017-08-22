<?php

use yii\db\Migration;
use common\models\trans\ar\Transaction;

class m170822_134416_transaction extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, Transaction::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `t_id` int(10) unsigned not null auto_increment comment '主键',
            `t_number` char(20) not null comment '交易编号',
            `t_succ_pay_type` char(12) not null default '' comment '成功支付的方式',
            `t_pay_status` char(12) not null comment '支付状态',
            `t_status` char(12) not null comment '交易状态',
            `t_type` char(12) not null comment '交易类型',
            `t_fee` int(10) unsigned not null comment '交易的费用',
            `t_fee_type` char(6) not null comment '交易费用的类型，货币类型',
            `t_pay_at` int(10) unsigned not null default 0 comment '支付时间',
            `t_invalid_at` int(10) unsigned not null default 0 comment '失效时间',
            `t_created_at` int(10) unsigned not null comment '创建时间',
            `t_updated_at` int(10) unsigned not null comment '更新时间',
            `t_module` char(12) not null comment '所属模块',
            `t_app_no` char(20) not null comment '应用编号',
            `t_timeout` int(10) unsigned not null comment '有效时间',
            `t_belong_uid` int(10) unsigned not null default 0 comment '所属用户id',
            `t_create_uid` int(10) unsigned not null default 0 comment '创建用户id',
            primary key (t_id)
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
