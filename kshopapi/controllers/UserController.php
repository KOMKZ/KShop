<?php
namespace kshopapi\controllers;

use Yii;
use common\controllers\ApiController;
use common\models\user\UserModel;
use common\models\user\query\UserQuery;
use yii\filters\auth\HttpBearerAuth;

/**
 *
 */
class UserController extends ApiController
{

    public function behaviors(){
        // todo 转移到api controller
        return array_merge(parent::behaviors(), [
            'bearerAuth' => [
                'class' => HttpBearerAuth::className(),
                'optional' => ['login']
            ]
        ]);
    }

    public function actionLogin(){
        $post = Yii::$app->request->getBodyParams();
        if(empty($post['u_email']) || empty($post['password']) || empty($post['type'])){
            return $this->error(403, Yii::t('app', "参数错误"));
        }
        $user = UserQuery::findActive()->andWhere(['u_email' => $post['u_email']])->one();
        if(!$user){
            return $this->error(404, Yii::t('app', "用户不存在/未激活"));
        }
        $uModel = new UserModel();
        if(!$uModel->validatePassword($user, $post['password'])){
            return $this->error(403, Yii::t('app', "密码错误"));
            return false;
        }
        $accessToken = UserModel::buildAccessToken();
        $expire = time() + 3600;
        $payload = [
            'user_info' => $user->toArray(),
            'token_info' => [
                'id' => $accessToken,
                'expire' => $expire
            ]
        ];
        $uModel->loginInAccessToken($user, $accessToken);
        $token = $uModel->buildToken($payload,  HttpBearerAuth::className());
        if(!$token){
            return $this->error('500', Yii::t('app', "系统生成access-token失败"));
        }
        return $this->succ(['jwt' => $token]);
    }

    public function actionLogout(){
        Yii::$app->user->logout();
        return $this->succ(true);
    }

}
