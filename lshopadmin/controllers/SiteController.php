<?php
namespace lshopadmin\controllers;

use Yii;
use common\controllers\AdminController as Controller;
use yii\web\HttpException;
use yii\web\UserException;
use common\models\staticdata\ConstMap;
use common\models\user\ar\User;
use common\models\user\UserModel;
use common\models\user\query\UserQuery;

/**
 *
 */
class SiteController extends Controller{
	public function actionIndex(){
		return $this->render('index');
	}
	public function actionLogin(){
		if(!Yii::$app->user->isGuest){
			return $this->redirect(['site/index']);
		}
		$user = new User;
		$uModel = new UserModel();
		$postData = Yii::$app->request->post("User");
		if($postData){
			$target = UserQuery::findActive()->andWhere(['u_email' => $postData['u_email']])->one();
			if($target && $uModel->validatePassword($target, $postData['password'])){
				$uModel->loginInSession($target);
				return $this->refresh();
			}
			if(!$target){
			 	$user->addError('u_email', '用户不存在');
			}else{
				$user->addError('password', '密码错误');
			}
		}
		return $this->render('login', [
			'model' => $user
		]);
	}
	public function actionError(){
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
			$name = Yii::t('app', '错误');
		}

		if ($code) {
			$name .= " (#$code)";
		}
		if ($exception instanceof UserException) {
			$message = $exception->getMessage();
		} else {
			$message = Yii::t('app', 'dev' == YII_ENV ? $exception->getMessage() : '服务器内部错误');
		}
		return $this->render('error');
	}
}
