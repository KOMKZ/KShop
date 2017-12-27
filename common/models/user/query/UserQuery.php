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

    public static function findSafeField(){
        return static::find()->select([
            'u_auth_status',
            'u_created_at',
            'u_email',
            'u_id',
            'u_status',
            'u_updated_at',
            'u_username'
        ]);
    }

    public static function findActive(){
        return static::find()
                     ->where(['u_status' => User::STATUS_ACTIVE]);
    }
}
