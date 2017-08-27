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
        // $notifyData = Yii::$app->params['wxpay_notification'];
        $notifyData = [
            'gmt_create' => '2017-08-23 17:47:30',
            'charset' => 'utf-8',
            'gmt_payment' => '2017-08-23 17:48:47',
            'notify_time' => '2017-08-23 17:48:48',
            'subject' => '测试产品',
            'sign' => 'gZJJGIZQ64c54ZP5Fo+jF775f2mfNkX8o/iWkLun6iX19zvxyqYQDhGyEHxJADNFuvAroFOgcKyTX6HWo8+zItxYZwkvuqmB2cC7aCeKGPdsHW6pb0DOIUC62tHIEkjijeKndEW1oDhWoN5dS33986cGXB53Cv45lBICv+A9Qvcyq1jg+rGy+bmumPvIRVBJ2CjjPSVFBSOa/lp39oDhqK7EwgYdqScm+FiCsyLNyE5Lv1M0SET/ztotp9fINejRtYLbPMs6G7sHEd7nyJOF/eMaFlFhqcBDmH2MIou2Tv3wFPVfCd/k0mr0XDoYQxaFqKfMKK/v4r/Ab9PLPldbBQ==',
            'buyer_id' => '2088102169564561',
            'invoice_amount' => '0.01',
            'version' => '1.0',
            'notify_id' => 'd75092765d6924ce3bd941a0e5ae217kbm',
            'fund_bill_list' => '[{\"amount\":\"0.01\",\"fundChannel\":\"ALIPAYACCOUNT\"}]',
            'notify_type' => 'trade_status_sync',
            'out_trade_no' => 'TR172017470723080006',
            'total_amount' => '0.01',
            'trade_status' => 'TRADE_SUCCESS',
            'trade_no' => '2017082321001004560200271526',
            'auth_app_id' => '2016101000649447',
            'receipt_amount' => '0.01',
            'point_amount' => '0.00',
            'app_id' => '2016101000649447',
            'buyer_pay_amount' => '0.01',
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
