<?php
namespace common\models\pay\payment;

use Yii;
use common\models\Model;
/**
 *
 */
class Alipay extends Model
{
    CONST NAME = 'alipay';
    CONST MODE_URL = 'url';
    CONST MODE_APP = 'app';

    public $gatewayUrl;
    public $appId;
    public $rsaPrivateKeyFilePath;
    public $alipayrsaPublicKey;
    public $apiVersion = '1.0';
    public $signType = 'RSA2';
    public $postCharset = 'utf-8';
    public $format = 'json';
    public $notifyUrl = '';
    public $returnUrl = '';
    public $orderTimeOut = '30m';
    private $_aopClient = null;
    public function init(){
        $this->_aopClient = new \AopClient();
        $this->_aopClient->gatewayUrl = $this->gatewayUrl;
        $this->_aopClient->appId = $this->appId;
        $this->_aopClient->rsaPrivateKeyFilePath = $this->rsaPrivateKeyFilePath;
        $this->_aopClient->alipayrsaPublicKey = $this->alipayrsaPublicKey;
        $this->_aopClient->apiVersion = $this->apiVersion;
        $this->_aopClient->signType = $this->signType;
        $this->_aopClient->postCharset = $this->postCharset;
        $this->_aopClient->format = $this->format;
    }

    public function formatReturn($masterData, $response){
        return [
            'master_data' => $masterData,
            'response' => $response,
        ];
    }

    public function createRefund($data){
        try {
            $request = new \AlipayTradeRefundRequest ();
            $request->setBizContent("{" .
            "\"out_trade_no\":\"" . $data['trans_number'] . "\"," .
            "\"refund_amount\":" . $data['trans_refun']/100 . "," .
            "\"refund_reason\":\"" . $data['trans_refund_reasons'] . "\"," .
            "  }");
            $result = $this->_aopClient->execute($request);
            console($result);
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError('', $e->getMessage());
            return false;
        }


    }

    public function checkOrderIsPayed($thirdOrder){
        if($thirdOrder
            && in_array($thirdOrder['trade_status'], ['TRADE_SUCCESS']))
        {
            return true;
        }
        return false;
    }
    public function queryOrder($data){
        try {
            $request = new \AlipayTradeQueryRequest();
            $request->setBizContent("{" .
                "\"out_trade_no\":" . "\"" . $data['trans_number'] . "\"" .
            "}"
            );
            $result = $this->_aopClient->execute($request);
            $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
            $resultCode = $result->$responseNode->code;
            if(!empty($resultCode) && $resultCode == 10000){
                // 注意,支付宝的订单的创建是需要一些触发动作,比如扫码之后才会有订单
                // 否则会一直报交易不存在记录
                return (array)$result->$responseNode;
            }{
                $this->addError('', Yii::t('app', sprintf('关闭支付宝订单失败:%s,%s,%s,%s',
                    $result->$responseNode->code,
                    $result->$responseNode->msg,
                    $result->$responseNode->sub_code,
                    $result->$responseNode->sub_msg
                )));
                return false;
            }

        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError('', $e->getMessage());
            return false;
        }
    }

    public function closeOrder($data){
        try {
            $request = new \AlipayTradeCloseRequest();
            $request->setBizContent("{" .
                "\"out_trade_no\":" . "\"" . $data['trans_number'] . "\"" .
            "}"
            );
            $result = $this->_aopClient->execute($request);

            $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
            $resultCode = $result->$responseNode->code;
            if(!empty($resultCode) && $resultCode == 10000){
                // 注意,支付宝的订单的创建是需要一些触发动作,比如扫码之后才会有订单
                // 否则会一直报交易不存在记录
                return (array)$result->$responseNode;
            }else{
                $this->addError('', Yii::t('app', sprintf('关闭支付宝订单失败:%s,%s,%s,%s',
                    $result->$responseNode->code,
                    $result->$responseNode->msg,
                    $result->$responseNode->sub_code,
                    $result->$responseNode->sub_msg
                )));
                return false;
            }
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError('', $e->getMessage());
            return false;
        }



    }

    public function createOrder($data, $type){
        try {
            switch ($type) {
                case static::MODE_URL:
                    $result = $this->createOrderForUrl($data);
                    return $this->formatReturn($result, $result);
                case static::MODE_APP:
                    $result = $this->createOrderForApp($data);
                    return $this->formatReturn($result, $result);
                default:
                    throw new InvalidArgumentException(Yii::t('app', "{$type}不支持的交易类型"));
                    break;
            }
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError('', $e->getMessage());
            return false;
        }
    }

    public function createOrderForUrl($data){
        $preOrder = new \AlipayTradePagePayRequest();
        if(!empty($data['trans_return_url'])){
            $preOrder->setReturnUrl($data['pay_return_url']);
        }else{
            $preOrder->setReturnUrl($this->returnUrl);
        }
        if(!empty($data['trans_invalid_at'])){
            $timeoutExpress = ($data['trans_invalid_at'] - $data['trans_start_at'])/60 . 'm';
        }else{
            $timeoutExpress = $this->$orderTimeOut;
        }
        $preOrder->setNotifyUrl($this->notifyUrl);
        $preOrder->setBizContent("{" .
            "\"timeout_express\":" . "\"" . $timeoutExpress . "\"," .
            "\"product_code\": \"FAST_INSTANT_TRADE_PAY\"," .
            "\"out_trade_no\":" . "\"" . $data['trans_number'] . "\"," .
            "\"subject\":" . "\"" . $data['trans_title'] . "\"," .
            "\"total_amount\":" . "\"" . ($data['trans_total_fee']/100) . "\"," .
            "\"body\":" . "\"" . $data['trans_detail'] . "\"" .
        "}"
        );
        $payUrl = $this->_aopClient->pageExecute($preOrder, 'get');
        return $payUrl;
    }

    public function createOrderForApp($data){
        $preOrder = new \AlipayTradeAppPayRequest();
        if(!empty($data['trans_return_url'])){
            $preOrder->setReturnUrl($data['pay_return_url']);
        }else{
            $preOrder->setReturnUrl($this->returnUrl);
        }
        if(!empty($data['trans_invalid_at'])){
            $timeoutExpress = ($data['trans_invalid_at'] - $data['trans_start_at'])/60 . 'm';
        }else{
            $timeoutExpress = $this->$orderTimeOut;
        }
        $preOrder->setNotifyUrl($this->notifyUrl);
        $preOrder->setBizContent("{" .
            "\"timeout_express\":" . "\"" . $timeoutExpress . "\"," .
            "\"product_code\": \"FAST_INSTANT_TRADE_PAY\"," .
            "\"out_trade_no\":" . "\"" . $data['trans_number'] . "\"," .
            "\"subject\":" . "\"" . $data['trans_title'] . "\"," .
            "\"total_amount\":" . "\"" . ($data['trans_total_fee']/100) . "\"," .
            "\"body\":" . "\"" . $data['trans_detail'] . "\"" .
        "}"
        );
        $paySignString = $this->_aopClient->sdkExecute($preOrder);
        return $paySignString;
    }





}
