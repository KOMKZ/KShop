<?php
namespace kshopadmin\controllers;

use Yii;
use common\controllers\AdminController as Controller;
use yii\web\HttpException;
use yii\web\UserException;
/**
 * a
 */
class SiteController extends Controller
{
    public function actionIndex(){
        return $this->render('index');
    }
    public function actionUpload(){
        console(2);
    }
    public function actionError(){
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
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
            return $this->render('error' ?: $this->id, [
                'name' => $name,
                'message' => $message,
                'exception' => $exception,
            ]);
        }
    }
}
