<?php
namespace common\models\user;

use Yii;
use common\models\Model;
use common\models\user\ar\User;
use common\models\staticdata\Errno;

/**
 *
 */
class UserModel extends Model
{
    public function createUser($data){
        $user = new User();
        if(!$user->load($data, '') || !$user->validate()){
            $this->addError('', $this->getOneErrMsg($user));
            return false;
        }
        $user->u_auth_key = User::NOT_AUTH == $user->u_auth_status ?
                            $this->buildAuthKey() : '';
        $user->u_status = User::NOT_AUTH == $user->u_auth_status ?
                            User::STATUS_NO_AUTH : $user->u_status;
        $user->u_password_hash = $this->buildPasswordHash($user->password);
        $user->u_password_reset_token = '';
        $user->u_created_at = time();
        $user->u_updated_at = time();
        if(!$user->insert(false)){
            $this->addError('', Errno::DB_INSERT_FAIL);
            return false;
        }
        return $user;
    }
    public function validatePassword($user, $password){
        return Yii::$app->security->validatePassword($password, $user->u_password_hash);
    }
    public function login($user, $password, $remember = false){
        if(empty($password)){
            $this->addError('', Yii::t('app', '密码不能为空'));
            return false;
        }
        $remember = (bool)$remember;
        if(!$this->validatePassword($user, $password)){
            $this->addError('', Yii::t('app', '密码错误'));
            return false;
        }
        Yii::$app->user->login($user, $remember ? 3600 * 24 * 30 : 0);
        return true;
    }

    protected function buildPasswordHash($password){
        return Yii::$app->security->generatePasswordHash($password);;
    }

    protected function buildAuthKey(){
        return Yii::$app->security->generateRandomString();
    }


}
