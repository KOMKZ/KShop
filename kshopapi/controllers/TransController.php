<?php
namespace kshopapi\controllers;

use Yii;
use common\controllers\ApiController as Controller;
use common\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\base\InvalidParamException;
use yii\web\ForbiddenHttpException;
use common\models\pay\PayModel;
/**
 *
 */
class TransController extends Controller{
    public function actionNotify($type){
       $notifyData = Yii::$app->request->getBodyParams();
       $payment = PayModel::getPayment($type);
       $result = $payment->handleNotify($notifyData, []);
       if($result['code'] > 0){
           $payment->sayFail([]);
           exit();
       }
       // todo
       // 1 记录notify数据
       // 2 业务查询
       // 3 触发事件
       $payment->saySucc([]);
       exit();
    }
}
