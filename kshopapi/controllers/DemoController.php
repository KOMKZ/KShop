<?php
namespace kshopapi\controllers;

use Yii;
use yii\web\Controller;
use common\models\pay\billparser\AliBillParser;
use common\models\pay\PayModel;

/**
 *
 */
class DemoController extends Controller
{
    public function actionIndex(){
        $alipay = Yii::$app->alipay;
        $aop = $alipay->getAopClient();
        $request = new \AlipayFundTransToaccountTransferRequest ();
        $request->setBizContent("{" .
        "\"out_biz_no\":\"31423214234321\"," .
        "\"payee_type\":\"ALIPAY_LOGONID\"," .
        "\"payee_account\":\"mapbbj2868@sandbox.com\"," .
        "\"amount\":\"12.23\"," .
        "\"payer_show_name\":\"上海交通卡退款\"," .
        "\"payee_real_name\":\"沙箱环境\"," .
        "\"remark\":\"转账备注\"" .
        "  }");
        $result = $aop->execute ( $request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        console($result);
    }
    public function actionLoad(){
        
    }
}
