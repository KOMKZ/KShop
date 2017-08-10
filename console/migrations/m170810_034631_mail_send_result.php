<?php

use yii\db\Migration;
use common\models\mail\ar\MailResult;

class m170810_034631_mail_send_result extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, MailResult::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `mail_send_id` int(10) unsigned not null auto_increment comment '主键',
            `mail_id` int(10) unsigned not null comment '邮件主体id',
            `mail_sender` varchar(255) not null comment '发件人',
            `mail_receipt` varchar(255) not null comment '收件人',
            `mail_status` char(30) not null comment '发件状态',
            `mail_error` varchar(255) not null default '0' comment '错误信息',
            `mail_consume` float(10.3) not null comment '消耗时间',
            `mail_send_at` integer(10) not null comment '发送时间',
            `mail_updated_at` integer(10) not null comment '更新时间',
            primary key (mail_send_id),
            index (mail_id)
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
