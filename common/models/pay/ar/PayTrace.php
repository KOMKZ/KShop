<?php
namespace common\models\pay\ar;

use yii\db\ActiveRecord;

/**
 *
 */
class PayTrace extends ActiveRecord
{
    public static function tableName(){
        return "{{%pay_trace}}";
    }
}
