<?php

use yii\db\Migration;
use common\models\action\ar\ActionLog;

class m170818_074116_action_log extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, ActionLog::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        CREATE TABLE `{$tableName}` (
          `al_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
          `al_module` smallint(3) unsigned NOT NULL COMMENT '模块id',
          `al_action` char(64) DEFAULT NULL,
          `al_uid` int(10) unsigned NOT NULL COMMENT '用户id',
          `al_object_id` int(10) unsigned NOT NULL COMMENT '对象id',
          `al_app_id` smallint(3) unsigned NOT NULL COMMENT 'APPid',
          `al_ip` varchar(100) DEFAULT '' COMMENT 'IP',
          `al_agent_info` text COMMENT '代理信息',
          `al_data` text COMMENT '相关数据',
          `al_created_time` int(10) unsigned NOT NULL COMMENT '创建时间',
          PRIMARY KEY (`al_id`),
          KEY `index04` (`al_module`,`al_action`,`al_object_id`),
          KEY `index03` (`al_module`,`al_action`,`al_uid`,`al_object_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COMMENT='动作记录'
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
