<?php
namespace common\models\transaction\ar;

use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;

/**
 *
 */
class Transaction extends ActiveRecord
{
    public static function tableName(){
        return "{{%transaction}}";
    }
}
