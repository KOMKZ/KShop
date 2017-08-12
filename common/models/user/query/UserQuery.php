<?php
namespace common\models\user\query;

use common\models\user\ar\User;
use yii\base\Object;

/**
 *
 */
class UserQuery extends Object
{
    public static function find(){
        return User::find();
    }
    public static function findActive(){
        return static::find()
                     ->where(['u_status' => User::STATUS_ACTIVE]);
    }
}
