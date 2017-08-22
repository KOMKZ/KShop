<?php

use yii\db\Migration;


class m170822_142949_pay_trace extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, PayTrace::tableName()));
    }
    public function safeUp(){
        return true;
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(

        );
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
