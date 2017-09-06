<?php
namespace common\models\user\query;

use yii\base\Object;
use common\models\user\ar\UserReceiverAddr;
/**
 *
 */
class UserReceiverAddrQuery extends Object
{
    public static function find(){
        return UserReceiverAddr::find();
    }
}
