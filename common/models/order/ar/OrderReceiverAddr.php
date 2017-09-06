<?php
namespace common\models\order\ar;

use yii\db\ActiveRecord;

/**
 *
 */
class OrderReceiverAddr extends ActiveRecord
{
    public static function tableName(){
        return "{{%order_reciver_addr}}";
    }
}
