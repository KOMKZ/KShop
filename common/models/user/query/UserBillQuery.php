<?php
namespace common\models\user\query;

use yii\base\Object;
use common\models\user\ar\UserBillRecord;
/**
 *
 */
class UserBillQuery extends Object
{
    public static function find(){
        return UserBillRecord::find();
    }
}
