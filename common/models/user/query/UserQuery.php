<?php
namespace common\models\user\query;

use common\models\user\ar\User;
use common\models\user\ar\UserExtend;
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
        $uTab = User::tableName();
        $uExtTab = UserExtend::tableName();
        $query = static::find()->select([
            // base
            "{$uTab}.u_auth_status",
            "{$uTab}.u_created_at",
            "{$uTab}.u_email",
            "{$uTab}.u_id",
            "{$uTab}.u_status",
            "{$uTab}.u_updated_at",
            "{$uTab}.u_username",
            // extend
            "{$uExtTab}.u_ext_id",
            "{$uExtTab}.u_avatar_id"
        ]);
        $query->joinWith('user_extend');
        return $query;
    }

    public static function findActive(){
        return static::find()
                     ->where(['u_status' => User::STATUS_ACTIVE]);
    }
}
