<?php
namespace common\models\express\ar;

use yii\db\ActiveRecord;

/**
 *
 */
class Express extends ActiveRecord
{

    public static function tableName(){
        return "{{%express}}";
    }

}
