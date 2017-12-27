<?php
namespace kshopapi\controllers;

use Yii;
use common\controllers\ApiController;
use common\models\user\UserModel;
use common\models\user\query\UserQuery;
use yii\base\ErrorException;
use common\filters\auth\HttpBearerAuth;

/**
 *
 */
class AuthController extends ApiController
{
	public function actionLogin(){
		$post = Yii::$app->request->getBodyParams();
		if(empty($post['u_email']) || empty($post['password']) || empty($post['type'])){
			Yii::$app->response->statusCode = 401;
			return $this->error(401, Yii::t('app', "参数错误"));
		}
		$user = UserQuery::findActive()->andWhere(['u_email' => $post['u_email']])->one();
		if(!$user){
			Yii::$app->response->statusCode = 401;
			return $this->error(401, Yii::t('app', "用户不存在/未激活"));
		}
		$uModel = new UserModel();
		if(!$uModel->validatePassword($user, $post['password'])){
			Yii::$app->response->statusCode = 401;
			return $this->error(401, Yii::t('app', "密码错误"));
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
			Yii::$app->response->statusCode = 401;
			return $this->error('401', Yii::t('app', "系统生成access-token失败"));
		}
		return $this->succ(['jwt' => $token]);
	}
	
	public function actionGetInfo(){
		$user = Yii::$app->user->identity;
		$userInfo = $user->toArray();
		$userInfo['u_role'] = "admin";
		$userInfo['u_avatar'] = "https://avatars2.githubusercontent.com/u/813734?s=88&v=4";
		return $this->succ($userInfo);
	}

	public function actionLogout(){
		Yii::$app->user->logout();
		return $this->succ(true);
	}

}
