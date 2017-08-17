<?php
namespace common\models\pay\ar;

use yii\db\ActiveRecord;

/**
 *
 */
class ThirdBill extends ActiveRecord
{
    public static function tableName(){
        return "{{%third_bill}}";
    }
}
