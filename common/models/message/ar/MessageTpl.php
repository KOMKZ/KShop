<?php
namespace common\models\message\ar;

use yii\db\ActiveRecord;
/**
 *
 */
class MessageTpl extends ActiveRecord
{
    public static function tableName(){
        return "{{%message_tpl}}";
    }
}
