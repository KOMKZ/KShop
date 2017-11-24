<?php
namespace kshopapi\controllers;

use Yii;
use common\controllers\ApiController;
use common\models\user\UserModel;
use common\models\user\query\UserQuery;
use yii\filters\auth\HttpBearerAuth;
use yii\base\ErrorException;

/**
 *
 */
class UserController extends ApiController{
	public function actionList(){
		$this->succ(1);
	}
	public function actionCreate(){
		$postData = Yii::$app->request->getBodyParams();
		$uModel = new UserModel();
		return $this->succ($postData);
		$user = $uModel->createUser($postData);
		if(!$user){
			return $this->error(null, $uModel->getErrors());
		}
		return $this->succ($user->toArray());
	}
}
