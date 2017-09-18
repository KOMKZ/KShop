<?php
namespace common\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;
/**
 *
 */
class AdminController extends Controller
{
	public $enableCsrfValidation = false;

	public function behaviors()
	{
		// todo 权限控制这样弄 不好拆卸
		// 比如哪天我就不需要安装权限控制了，这里不够灵活
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						// 'roles' => [$this->route]
						'roles' => ['@']
					]
				]
			]
		];
	}

	public function getApiRoute($route, $real = false){
		if(!$real){
			$route[0] = '/api/' . $route[0];
		}
		return $real ?
			   Yii::$app->apiurl->createAbsoluteUrl($route)
			   :
			   Url::to($route);
	}
}
