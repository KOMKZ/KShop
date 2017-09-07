<?php
namespace common\models\order\ar;

use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
use yii\behaviors\TimestampBehavior;

/**
 *
 */
class OrderExpress extends ActiveRecord
{
    CONST STATUS_ORDER_INIT = "order_init";

    CONST TTYPE_ORDER = 'consume_order';// 订单消费类物流订单

    public static function tableName(){
        return "{{%order_express}}";
    }


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'od_express_created_at',
                'updatedAtAttribute' => 'od_express_updated_at'
            ]
        ];
    }
    public function rules(){
        return [
            ['od_express_fee', 'integer']
            ,['od_express_fee', 'default', 'value' => 0]


            ,['od_express_number', 'string']

            ,['od_express_status', 'default', 'value' => self::STATUS_ORDER_INIT]
            ,['od_express_status', 'in', 'range' => ConstMap::getConst('od_express_status', true)]

            ,['od_express_target_type', 'default', 'value' => self::TTYPE_ORDER]
            ,['od_express_target_type', 'in', 'range' => ConstMap::getConst('od_express_target_type', true)]

            ,['od_express_comment', 'default', 'value' => '']

            ,['od_express_type', 'default', 'value' => '']

        ];
    }
}
