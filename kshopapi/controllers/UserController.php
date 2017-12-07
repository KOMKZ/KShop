<?php
namespace kshopapi\controllers;

use Yii;
use common\controllers\ApiController;
use common\models\user\UserModel;
use common\models\user\query\UserQuery;
use yii\filters\auth\HttpBearerAuth;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 *
 */
class UserController extends ApiController{
	public function actionList(){
		$getData = Yii::$app->request->get();
		$defaultOrder = [];
		if(!empty($getData['orderBy'])){
			$defaultOrder[$getData['orderBy']] = 1 == ArrayHelper::getValue($getData, 'ascending', 1) ? SORT_DESC : SORT_ASC;
		}
		$provider = new ActiveDataProvider([
			'query' => UserQuery::findSafeField()->asArray(),
			'sort' => [
				'defaultOrder' => $defaultOrder
			]
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
