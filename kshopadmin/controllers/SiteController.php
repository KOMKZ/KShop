<?php
namespace kshopadmin\controllers;

use Yii;
use common\controllers\AdminController as Controller;
use yii\web\HttpException;
use yii\web\UserException;
use common\models\user\ar\User;
use yii\filters\AccessControl;
/**
 * a
 */
class SiteController extends Controller
{

	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'actions' => ['error', 'login'],
						'allow' => true,
					],
					[
						'allow' => true,
						'roles' => ['@'],
					]
				],
			],
		];
	}
	public function actionIndex(){
		return $this->render('index');
	}
	public function actionError()
	{
		if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
			// action has been invoked not from error handler, but by direct route, so we display '404 Not Found'
			$exception = new HttpException(404, Yii::t('yii', 'Page not found.'));
		}

		if ($exception instanceof HttpException) {
			$code = $exception->statusCode;
		} else {
			$code = $exception->getCode();
		}

		if ($exception instanceof \Exception) {
			$name = $exception->getName();
		} else {
			$name = Yii::t('yii', 'Error');
		}
		if ($code) {
			$name .= " (#$code)";
		}

		if ($exception instanceof UserException) {
			$message = $exception->getMessage();
		} else {
			$message = Yii::t('yii', $exception->getMessage());
		}
		if (Yii::$app->getRequest()->getIsAjax()) {
			return "$name: $message";
		} else {
			if(Yii::$app->user->isGuest){
				return $this->redirect(['site/login']);
			}
			return $this->render('error' ?: $this->id, [
				'name' => $name,
				'message' => $message,
				'exception' => $exception,
			]);
		}
	}
	public function actionLogin(){
		if (!Yii::$app->user->isGuest) {
			return $this->goHome();
		}
		$user = new User();
		return $this->render('login', [
			'model' => $user,
			'routes' => [
				'login_action' => $this->getApiRoute(['user/login'])
			]
		]);
	}
}
