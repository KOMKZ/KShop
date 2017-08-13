<?php
namespace common\models\user\ar;

use yii\db\ActiveRecord;

/**
 *
 */
class UserMessage extends ActiveRecord
{
    public static function tableName(){
        return "{{%user_message}}";
    }
}
