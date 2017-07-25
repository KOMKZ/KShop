<?php
namespace common\models\pay\payment;

use Yii;
use WxPayConfig;
use WxPayUnifiedOrder;
use WxPayOrderQuery;
use WxPayApi;
use WxPayRefund;
use WxPayRefundQuery;
use WxPayCloseOrder;
use common\models\Model;
use yii\base\InvalidArgumentException;
/**
 *
 */
class Wxpay extends Model
{
    const NAME = 'wxpay';
    const MODE_NATIVE = 'NATIVE';
    CONST MODE_APP = 'APP';

    public $appid;

    public $mchid;

    public $key;

    public $appsecret;

    public $sslcert_path;

    public $sslkey_path;

    public $notifyUrl;
    public function init(){
        parent::init();
        WxPayConfig::$APPID = $this->appid;
        WxPayConfig::$MCHID = $this->mchid;
        WxPayConfig::$KEY = $this->key;
        WxPayConfig::$APPSECRET = $this->appsecret;
        WxPayConfig::$SSLCERT_PATH = $this->sslcert_path;
        WxPayConfig::$SSLKEY_PATH = $this->sslkey_path;
    }
    public function formatReturn($masterData, $response){
        return [
            'master_data' => $masterData,
            'response' => $response,
        ];
    }

    public function handleNotify($notifyData, $params = []){

    }

    public function queryRefund($data){
        try {
            $input = new WxPayRefundQuery();
            $input->SetOut_trade_no($data["trans_number"]);
            $result = WxPayApi::refundQuery($input);
            if(!$this->checkResultIsValid($result)){
                return false;
            }
            return $result;
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError('', $e->getMessage());
            return false;
        }
    }

    public function checkOrderIsRefunded($data){
        $result = $this->queryRefund($data);
        if($result
            //todo
           && in_array($result['refund_status_0'], ['SUCCESS'])
        ){
            return true;
        }
        return false;
    }

    public function closeOrder($data){
        try {
            $input = new WxPayCloseOrder();
            $input->SetOut_trade_no($data["trans_number"]);
            $result = WxPayApi::closeOrder($input);
            if(!$this->checkResultIsValid($result)){
                return false;
            }
            return $result;
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError('', $e->getMessage());
            return false;
        }
    }

    public function createRefund($data){
        try {
            ini_set('date.timezone','Asia/Shanghai');
        	$input = new WxPayRefund();
        	$input->SetOut_trade_no($data["trans_number"]);
        	$input->SetTotal_fee($data["trans_total_fee"]);
        	$input->SetRefund_fee($data["trans_refund_fee"]);
            $input->SetOut_refund_no($data["trans_refund_number"]);
            $input->SetOp_user_id(WxPayConfig::$MCHID);
        	$result = WxPayApi::refund($input);
            if(!$this->checkResultIsValid($result)){
                return false;
            }
            return $result;
        } catch (\Exception $e) {
            // crul 58 证书错误,路径或者内容无效
            Yii::error($e);
            $this->addError('', $e->getMessage());
            return false;
        }
    }
    public function queryOrder($data){
        try {
            $input = new WxPayOrderQuery();
            $input->SetOut_trade_no($data['trans_number']);
            $result = WxPayApi::orderQuery($input);
            if(!$this->checkResultIsValid($result)){
                return false;
            }
            return $result;
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError('', $e->getMessage());
            return false;
        }
    }

    public function checkOrderIsPayed($data){
        $result = $this->queryOrder($data);
        if($result
            && in_array($result['trade_state'], ['SUCCESS']))
        {
            return true;
        }
        return false;
    }
    public function createOrder($data, $type){
        try {
            switch ($type) {
                case static::MODE_NATIVE:
                    $result = $this->createNativeOrder($data);
                    if(!$this->checkResultIsValid($result)){
                        return false;
                    }
                    return $this->formatReturn($result['code_url'], $result);
                case static::MODE_APP:
                    $result = $this->createAppOrder($data);
                    if(!$this->checkResultIsValid($result)){
                        return false;
                    }
                    $data = $this->signDataForApp($result);
                    return $this->formatReturn($data, $result);
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

    protected function checkResultIsValid($result){
        if('FAIL' == $result['return_code']){
            // https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_1
            // 错误代码列表
            $this->addError('', Yii::t("app", "微信响应错误:{$result['return_msg']}"));
            return false;
        }
        if('FAIL' == $result['result_code']){
            // error https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_4
            $this->addError('', Yii::t("app", "微信响应错误:{$result['err_code']}.{$result['err_code_des']}"));
            return false;
        }
        return true;
    }

    public function createAppOrder($data){
        try {
            $input = new WxPayUnifiedOrder();
            $input->SetBody($data['trans_body']);
            $input->SetOut_trade_no($data['trans_number']);
            $input->SetTotal_fee($data['trans_total_fee']);
            $input->SetTime_start(date("YmdHis", $data['trans_start_at']));
            $input->SetTime_expire(date("YmdHis", $data['trans_invalid_at']));
            $input->SetNotify_url($this->notifyUrl);
            $input->SetTrade_type("APP");
            $input->SetProduct_id($data['trans_module_number']);
            $result = WxPayApi::unifiedOrder($input);
            return $result;
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError('', $e->getMessage());
            return false;
        }


    }

    protected function signDataForApp($data){
        $params = [];
        $params['appid'] = $data['appid'];
        $params['timestamp'] = '' . time();
        $params['noncestr'] = md5(uniqid(mt_rand(), true));
        $params['package'] = 'Sign=WXPay';
        $params['prepayid'] = $data['prepay_id'];
        $params['partnerid'] = $data['mch_id'];
        $params['sign'] =  static::getDataSignature($params);
        return $params;
    }

    public function createNativeOrder($data){
        $input = new WxPayUnifiedOrder();
        $input->SetBody($data['trans_body']);
        $input->SetOut_trade_no($data['trans_number']);
        $input->SetTotal_fee($data['trans_total_fee']);
        $input->SetTime_start(date("YmdHis", $data['trans_start_at']));
        $input->SetTime_expire(date("YmdHis", $data['trans_invalid_at']));
        $input->SetNotify_url($this->notifyUrl);
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($data['trans_module_number']);
        $result = WxPayApi::unifiedOrder($input);
        return $result;
    }


    protected static function getDataSignature($values){
        ksort($values);
        $string = self::toUrlParams($values);
        $string = $string . "&key=".WxPayConfig::$KEY;
        $string = md5($string);
        $result = strtoupper($string);
        return $result;
    }
    protected static function toUrlParams($values)
    {
        $buff = "";
        foreach ($values as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
}
