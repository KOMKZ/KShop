<?php

use yii\db\Migration;
use common\models\goods\ar\GoodsAttr;


class m170723_145608_goods_attrs extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, GoodsAttr::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        create table `{$tableName}`(
            `g_atr_id` int(10) unsigned not null auto_increment comment '主键',
            `g_atr_code` varchar(60) not null comment '商品属性码',
            `g_atr_name` varchar(60) not null comment '商品属性名',
            `g_atr_show_name` varchar(60) not null comment '商品展示属性名',
            `g_atr_cls_id` int(10) unsigned not null comment '商品属性所属分类',
            `g_atr_created_at` int(10) unsigned not null comment '商品属性创建时间',
            primary key `g_atr_id` (g_atr_id)
        );
        ";
        $this->execute($createTabelSql);
        $this->batchInsert($this->getTableName(), [
            'g_atr_id',
            'g_atr_code',
            'g_atr_name',
            'g_atr_show_name',
            'g_atr_cls_id',
            'g_atr_created_at'
            ] ,[
            ['g_atr_id' => 1, 'g_atr_code' => 'model_number', 'g_atr_name' => '型号', 'g_atr_show_name' => '型号', 'g_atr_cls_id' => 1, 'g_atr_created_at' => time()],
            ['g_atr_id' => 2, 'g_atr_code' => 'memory', 'g_atr_name' => '内存', 'g_atr_show_name' => '内存', 'g_atr_cls_id' => 2, 'g_atr_created_at' => time()],
            ['g_atr_id' => 3, 'g_atr_code' => 'cpu', 'g_atr_name' => '处理器', 'g_atr_show_name' => '处理器', 'g_atr_cls_id' => 2, 'g_atr_created_at' => time()],
            ['g_atr_id' => 4, 'g_atr_code' => 'memory', 'g_atr_name' => '内存', 'g_atr_show_name' => '内存', 'g_atr_cls_id' => 3, 'g_atr_created_at' => time()],
            ['g_atr_id' => 5, 'g_atr_code' => 'color', 'g_atr_name' => '内存', 'g_atr_show_name' => '内存', 'g_atr_cls_id' => 3, 'g_atr_created_at' => time()],



        ]);
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
