<?php
namespace kshopapi\controllers;

use Yii;
use common\controllers\ApiController as Controller;
use common\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\base\InvalidParamException;
use yii\web\ForbiddenHttpException;
use common\models\pay\PayModel;
use common\models\pay\query\PayTraceQuery;
/**
 *
 */
class TransController extends Controller{
    public function actionNotify($type){
        Yii::$app->db->beginTransaction();
        $notifyData = Yii::$app->request->getBodyParams();
        Yii::error($notifyData);
        // $notifyData = Yii::$app->params['wxpay_notification'];
        $notifyData = [
            'gmt_create' => '2017-09-07 22:52:27',
            'charset' => 'utf-8',
            'gmt_payment' => '2017-09-07 22:52:35',
            'notify_time' => '2017-09-07 22:52:36',
            'subject' => 'IPhone7 内存-256G;颜色-金色 等多件',
            'sign' => 'F0q1r+XUeCXWd2SKWmmnG0X9epjEcWjftMLlqn3eQWv+RpcsFpMDgqz7zhSB5opDHzW4TNRWFEhLik6UdcIOqfO0jhcFnHd10ZLpIYo3iQeM64uDK8xTNXoxCdE/jLuXdaWHNtp/i+jUqYnA1c2rDjzORDJeQnV4cpFfGrLulGcdy4z8JmnHbVRx21cc6TG7p0HdoXIE7HGJ3hTmhJMG7+Aa6/s8+rgccmS4yoM2YooV5IjJUPIvN4vuEkC/ffpeK5HBvzslQEkRP+fhTiBuDbHZvgLEm+FtA/naraZQQSu9jI8C0vxDiVDE9XZEIs/doIbicqfxfkVm0843D2Al/g==',
            'buyer_id' => '2088102169564561',
            'body' => 'IPhone7 内存-256G;颜色-金色 x2、 IPhone7 内存-128G;颜色-金色 x1。 订单编号:OD222017484507094638',
            'invoice_amount' => '18675.00',
            'version' => '1.0',
            'notify_id' => '38d4788df47c238adb41c3d24d76d01kbm',
            'fund_bill_list' => '[{\"amount\":\"18675.00\",\"fundChannel\":\"ALIPAYACCOUNT\"}]',
            'notify_type' => 'trade_status_sync',
            'out_trade_no' => 'TR222017484507096264',
            'total_amount' => '18675.00',
            'trade_status' => 'TRADE_SUCCESS',
            'trade_no' => '2017090721001004560200274988',
            'auth_app_id' => '2016101000649447',
            'receipt_amount' => '18675.00',
            'point_amount' => '0.00',
            'app_id' => '2016101000649447',
            'buyer_pay_amount' => '18675.00',
            'sign_type' => 'RSA2',
            'seller_id' => '2088102178864092',
        ];


        $payment = PayModel::getPayment($type);
        try {
            $transData = $payment->handleNotify($notifyData, []);
            if($transData['code'] > 0){
               $payment->sayFail([]);
               exit();
            }
            $payOrder = PayTraceQuery::find()->andWhere(['pt_belong_trans_number' => $transData['trans_number']])->one();
            $payModel = new PayModel();
            if(!$payOrder || !$payModel->updatePayOrderPayed($payOrder, ['notification' => $notifyData])){
               $payment->sayFail([]);
               exit();
            }
            PayModel::triggerPayed($payOrder);
            $payment->saySucc([]);
            exit();
        } catch (\Exception $e) {
            Yii::error($e);
            $payment->sayFail([]);
            exit();
        }
    }
}
