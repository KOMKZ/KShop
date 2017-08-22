<?php
namespace common\models\user\ar;

use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
use common\models\message\MsgMode;
/**
 *
 */
class UserBillRecord extends ActiveRecord
{
    public static function tableName(){
        return "{{%user_bill_record}}";
    }
}
