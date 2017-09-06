<?php
namespace common\models\order\ar\OrderExpress;

use yii\db\ActiveRecord;

/**
 *
 */
class OrderExpress extends ActiveRecord
{
    public static function tableName(){
        return "{{%order_express}}";
    }
    
}
