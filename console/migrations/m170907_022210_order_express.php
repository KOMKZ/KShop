<?php

use yii\db\Migration;
use common\models\order\ar\OrderExpress;
class m170907_022210_order_express extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, OrderExpress::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        CREATE TABLE `{$tableName}` (
          `od_express_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
          `od_id` int(10) unsigned not null comment '订单id',
          `od_express_fee` int(10) unsigned not null default 0 comment '快递费用',
          `od_express_number` char(15) not null default '' comment '快递单号',
          `od_express_status` char(12) not null  comment '订单快递状态',
          `od_express_type` char(12) not null default '' comment '快递类型',
          `od_express_target_type` char(15) not null default '' comment '快递业务类型,如消费,退款',
          `od_express_comment` varchar(255) not null default '' comment '备注信息',
          `od_express_created_at` int(10) unsigned not null comment '创建时间',
          `od_express_updated_at` int(10) unsigned not null comment '更新时间',
          primary key (od_express_id)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单物流数据'
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
