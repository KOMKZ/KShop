<?php
namespace common\models\action\ar;

use yii\db\ActiveRecord;

/**
 *
 */
class ActionLog extends ActiveRecord
{
    public static function tableName(){
        return "{%action_log}";
    }


}
