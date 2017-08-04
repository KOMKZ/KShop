<?php

use yii\db\Migration;
use common\models\operation\ar\OperationLog;
class m170804_015448_operation_log extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, OperationLog::tableName()));
    }
    public function safeUp(){
        return true;
        $tableName = $this->getTableName();
        $createTabelSql = "
        CREATE TABLE `{$tableName}` (
          `ol_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
          `ol_module` smallint(3) unsigned NOT NULL COMMENT '模块id',
          `ol_action` char(64) DEFAULT NULL,
          `ol_uid` int(10) unsigned NOT NULL COMMENT '用户id',
          `ol_object_id` int(10) unsigned NOT NULL COMMENT '对象id',
          `ol_app_id` smallint(3) unsigned NOT NULL COMMENT 'APPid',
          `ol_ip` varchar(100) DEFAULT '' COMMENT 'IP',
          `ol_agent_info` text COMMENT '代理信息',
          `ol_data` text COMMENT '相关数据',
          `ol_created_time` int(10) unsigned NOT NULL COMMENT '创建时间',
          PRIMARY KEY (`ol_id`),
          KEY `index04` (`ol_module`,`ol_action`,`ol_object_id`),
          KEY `index03` (`ol_module`,`ol_action`,`ol_uid`,`ol_object_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='动作记录'
        ";
        $this->execute($createTabelSql);
        return true;
    }
    public function safeDown(){
        return true;
        $tableName = $this->getTableName();
        $dropTableSql = "
        drop table if exists {$tableName}
        ";
        $this->execute($dropTableSql);
        return true;
    }
}
