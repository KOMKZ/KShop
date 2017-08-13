<?php
namespace common\models\message\ar;

use yii\db\ActiveRecord;

/**
 *
 */
class MessageMap extends ActiveRecord
{
    public static function tableName(){
        return "{{%message_map}}";
    }
}
