<?php

use yii\db\Migration;
use common\models\goods\ar\GoodsDetail;

class m170801_064854_goods_detail extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, GoodsDetail::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `gd_id` int(10) unsigned not null auto_increment comment '主键',
            `g_id` int(10) unsigned not null comment '商品id',
            `g_intro_text` text not null comment '商品详细介绍',
            primary key `gd_id` (gd_id)
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
