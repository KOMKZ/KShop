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
		$behaviors['bearerAuth']['optional'][] = 'api/index';
		$behaviors['bearerAuth']['optional'][] = 'api/error';
		$behaviors['bearerAuth']['optional'][] = 'api/enums-map';
		return $behaviors;
	}

	public function actionEnumsMap(){
		return $this->succ(ConstMap::getConst());
	}

	public function actionIndex(){
		return $this->succ('Welcome to KShop');
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
		return $this->error($code, $message);
	}
}
