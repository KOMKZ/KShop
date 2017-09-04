<?php

use yii\db\Migration;
use common\models\order\ar\Order;

class m170829_144441_order extends Migration
{
    public function getTableName(){
        return preg_replace('/[\{\}]/', '', preg_replace("/%/", Yii::$app->db->tablePrefix, Order::tableName()));
    }
    public function safeUp(){
        $tableName = $this->getTableName();
        $createTabelSql = "
        CREATE TABLE `{$tableName}` (
          `od_id` int(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单主键',
          `od_type` char(12) not null COMMENT '订单类型',
          `od_title` varchar(255) not null COMMENT '订单标题',
          `od_pay_status` char(12) not null COMMENT '订单支付状态',
          `od_price` int(10) unsigned not null default 0 comment '订单价格',
          `od_origin_price` int(10) unsigned not null default 0 comment '订单原始价格',
          `od_comment_status` char(12) not null COMMENT '订单评论状态',
          `od_refund_status` char(12) not null COMMENT '订单退款状态',
          `od_status` char(12) not null COMMENT '订单状态',
          `od_belong_storage` int(10) not null default 0 comment '订单所属仓库',
          `od_logistics_status` char(12) not null COMMENT '订单物流状态',
          `od_succ_pay_type` char(12) not null default '' COMMENT '订单成功的支付方式',
          `od_pay_mode` char(12) not null COMMENT '订单付款方式，分期付款，货到付款，线上全额付款',
          `od_belong_uid` int(10) unsigned not null COMMENT '订单所属用户id',
          `od_operator_uid` int(10) unsigned not null COMMENT '订单审核用户id',
          `od_pid` int(10) unsigned not null default 0 COMMENT '订单父订单id',
          `od_number` char(20) not null COMMENT '订单编号',
          `od_created_at` int(10) unsigned not null COMMENT '订单创建时间',
          `od_payed_at` int(10) unsigned not null default 0 comment '订单支付的时间',
          `od_invalid_at` int(10) unsigned not null default 0 comment '订单失效的时间',
          `od_updated_at` int(10) unsigned not null default 0 comment '订单更新时间',
          primary key (od_id)
        ) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COMMENT='订单表'
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
