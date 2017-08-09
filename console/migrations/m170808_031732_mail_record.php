<?php

use yii\db\Migration;
use common\models\mail\ar\Mail;

class m170808_031732_mail_record extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, Mail::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `mail_id` int(10) unsigned not null auto_increment comment '主键',
            `mail_meta_data` text not null comment '邮件发送元数据',
            `mail_title` varchar(255) not null comment '邮件标题',
            `mail_type` char(12) not null default '' comment '邮件类型/模板',
            `mail_content` text not null comment '邮件的内容',
            `mail_content_type` varchar(64) not null comment '内容的类型',
            `mail_attachments` text null comment '邮件的附件',
            `mail_create_uid` integer(10) unsigned not null comment '邮件创建用户',
            `mail_is_cron` smallint(3) unsigned not null comment '是否是cron邮件',
            `mail_created_at` integer(10) unsigned not null comment '邮件的创建时间',
            primary key `mail_id` (mail_id)
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
