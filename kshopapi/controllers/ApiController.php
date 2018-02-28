<?php
namespace kshopapi\controllers;

use Yii;
use common\controllers\ApiController as Controller;
use yii\web\HttpException;
use yii\web\UserException;
use common\models\staticdata\ConstMap;
/**
 *
 */
class ApiController extends Controller
{
	public function behaviors(){
		$behaviors = parent::behaviors();
		$ignoreRoutes = array_keys(Yii::$app->authManager->getPermissionsByRole('vistor'));
		foreach($ignoreRoutes as $name){
			$behaviors['bearerAuth']['optional'][] = $name;
		}
		return $behaviors;
	}




}
