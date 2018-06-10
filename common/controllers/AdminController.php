<?php
namespace common\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\base\ActionEvent;
/**
 *
 */
class AdminController extends Controller
{
	public $enableCsrfValidation = false;
	public function getAdminSession(){
		return Yii::$app->user->identity;
	}
	public function behaviors()
	{
		return [];
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
	// public function afterAction($action, $result)
	// {
	// 	// todo 这种方法课程会导致循环重定向
	// 	Yii::$app->user->returnUrl= [Yii::$app->requestedRoute];
	// 	$event = new ActionEvent($action);
	// 	$event->result = $result;
	// 	$this->trigger(self::EVENT_AFTER_ACTION, $event);
	// 	return $event->result;
	// }
	public function setReturnUrl($route = null){
		$route ?
		Yii::$app->user->returnUrl= [$route]
		:
		Yii::$app->user->returnUrl= array_merge([Yii::$app->requestedRoute], Yii::$app->request->getQueryParams());
	}
	public function setWarning($msg){
		Yii::$app->session->setFlash('warning', Yii::t('app', $msg));
	}
	public function setDanger($msg){
		Yii::$app->session->setFlash('danger', Yii::t('app', $msg));
	}
	public function setError($msg){
		Yii::$app->session->setFlash('error', Yii::t('app', $msg));
	}
	public function setErrorFromErrors($errors){
		if(!empty($errors[''])){
			$this->setError(implode(",", $errors[""]));
		}
	}
	public function setSuccess($msg){
		Yii::$app->session->setFlash('success', Yii::t('app', $msg));
	}
	public function setInfo($msg, $noTras = false){
		Yii::$app->session->setFlash('info', $noTras ? $msg : Yii::t('app', $msg));
	}
	public function setCreateSuccess($msg = ''){
		$this->setSuccess($msg ? $msg : "创建成功");
	}
	public function setDeleteSuccess($msg = ''){
		$this->setSuccess($msg ? $msg : "删除成功");
	}
	public function setUpdateSuccess($msg = ''){
		$this->setSuccess($msg ? $msg : "更新成功");
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
