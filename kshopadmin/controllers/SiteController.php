<?php
namespace kshopadmin\controllers;

use Yii;
use common\controllers\AdminController as Controller;
use yii\web\HttpException;
use yii\web\UserException;
use common\models\user\ar\User;
use common\models\user\query\UserQuery;
use common\models\user\UserModel;
use yii\filters\AccessControl;
use yii\helpers\Url;
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

	public function actionLogin(){
		$userObject = UserQuery::findActive()->andWhere(['u_email' => '784248377@qq.com'])->one();

		if (!Yii::$app->user->isGuest) {
			return $this->goHome();
		}
		$user = new User();
		$userModel = new UserModel;
		if(Yii::$app->request->isPost){
			$postData = Yii::$app->request->getBodyParams();
			$user->scenario = 'login';
			if($user->load($postData) && $user->validate()){
				$userObject = UserQuery::findActive()->andWhere(['u_email' => $user->u_email])->one();
				if(!$userObject){
					$user->addError('u_email', Yii::t('app', "指定的用户不存在"));
				}elseif(!$userModel->validatePassword($userObject, $user->password)){
					$user->addError('password', Yii::t('app', "用户密码不正确"));
				}else{

					$userModel->loginInSession($userObject, $user->rememberMe);
					return $this->goHome();
				}
			}
		}
		return $this->render('login', [
			'model' => $user,
			'routes' => [
				'login_action' => Url::to(['site/login']),
			]
		]);
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

}
