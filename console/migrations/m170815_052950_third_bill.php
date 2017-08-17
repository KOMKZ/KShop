<?php

use yii\db\Migration;
use common\models\pay\ar\ThirdBill;


class m170815_052950_third_bill extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, ThirdBill::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `tb_id` int(10) unsigned not null auto_increment comment '主键',
            `tb_out_trade_no` char(20) not null comment '交易号',
            `tb_trade_no` varchar(255) not null comment '第三方交易号',
            `tb_type` char(20) not null comment '交易类型',
            `tb_pay_type` char(12) not null comment '第三方类型',
            `tb_created_at` int(10) unsigned not null comment '创建时间',
            `tb_no` varchar(64) not null comment '对账批次',
            `tb_bill_data` text not null comment '账单内容',
            `tb_third_app_id` varchar(255) null comment '第三方应用id',
            `tb_app_type` char(10) null comment '所属应用模块',
            primary key (tb_id)
        )CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB;
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
