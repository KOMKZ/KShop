<?php
namespace common\models\order\ar;

use yii\db\ActiveRecord;

/**
 *
 */
class OrderDiscount extends ActiveRecord
{
    public static function tableName(){
        return "{{%order_discount}}";
    }



}
