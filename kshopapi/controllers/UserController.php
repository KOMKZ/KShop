<?php
namespace kshopapi\controllers;

use Yii;
use common\controllers\ApiController;
use common\models\user\UserModel;
use common\models\user\query\UserQuery;
use yii\filters\auth\HttpBearerAuth;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

/**
 *
 */
class UserController extends ApiController{
	public function actionList(){
		sleep(10);
		$provider = new ActiveDataProvider([
			'query' => UserQuery::findSafeField()->asArray()
		]);
		return $this->succItems($provider->getModels(), $provider->totalCount);
	}
	public function actionCreate(){
		$postData = Yii::$app->request->getBodyParams();
		$uModel = new UserModel();
		$user = $uModel->createUser($postData);
		if(!$user){
			return $this->error(null, $uModel->getErrors());
		}
		return $this->succ($user->toArray());
	}
}
