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
	public function actionUpdate(){
		$postData = Yii::$app->request->getBodyParams();
		if(empty($postData['u_id'])){
			return $this->error(400, Yii::t('app',"参数不完整，没有指定用户u_id"));
		}
		$user = UserQuery::findSafeField()->andWhere(['u_id' => $postData['u_id']])->one();
		if(!$user){
			return $this->error(404, Yii::t('app', "指定的用户不存在"));
		}
		$uModel = new UserModel();
		$result = $uModel->updateUser($user, $postData);
		if(!$result){
			return $this->error(null, $uModel->getErrors());
		}
		return $this->succ($user->toArray());
	}
	public function actionView($u_id){
		$user = UserQuery::findSafeField()->andWhere(['u_id' => $u_id])->one();
		if(!$user){
			return $this->error(404, Yii::t("app", "指定的用户不存在"));
		}
		return $this->succ($user);
	}
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
