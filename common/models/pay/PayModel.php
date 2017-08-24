<?php
namespace common\models\pay;

use Yii;
use common\models\Model;
use common\models\pay\payment\Wxpay;
use common\models\pay\payment\Alipay;
use yii\base\InvalidArgumentException;
use common\models\pay\ar\ThirdBill;
use common\models\trans\ar\Transaction;
use common\models\pay\ar\PayTrace;
use common\models\staticdata\Errno;
/**
 *
 */
class PayModel extends Model
{
    CONST NOTIFY_INVALID = 'notify_invalid';
    CONST NOTIFY_ORDER_INVALID = 'notify_order_invalid';
    CONST NOTIFY_EXCEPTION = 'notify_exception';

    public static $map = [
        Alipay::NAME => [
            PayTrace::TYPE_DATA => Alipay::MODE_APP,
            PayTrace::TYPE_URL => Alipay::MODE_URL
        ],
        Wxpay::NAME => [
            PayTrace::TYPE_DATA => Wxpay::MODE_APP,
            PayTrace::TYPE_URL => Wxpay::MODE_NATIVE
        ]
    ];

    public static function getPayment($type){
        switch ($type) {
            case Wxpay::NAME:
                return Yii::$app->wxpay;
            case Alipay::NAME:
                return Yii::$app->alipay;
            default:
                throw new InvalidArgumentException(Yii::t('app', "{$type}不支持的支付类型"));
                break;
        }
    }
    public static function saveBillInDb($data){
        return Yii::$app->db->createCommand()->insert(ThirdBill::tableName(), $data)->execute();
    }

    /**
     * 创建一个第三方交易预支付订单
     * @param  array      $data   第三方预支付订单数据
     * - pt_pay_type: string,required 支付类型
     * - pt_pre_order_type: string,required 预支付数据的类型
     * url:
     * 支付宝:链接, 微信:二维码数据
     * data:
     * 支付宝:订单字符串 微信:订单数据
     * - pt_timeout: integer 预支付订单的失效时间
     * 如果没有定义则使用交易的失效时间
     * @param  Transaction $trans [description]
     * @return [type]             [description]
     */
    public function createPreOrder($data, Transaction $trans){
        $t = Yii::$app->db->beginTransaction();
        try {
            $data['pt_belong_trans_number'] = $trans->t_number;
            if(empty($data['pt_timeout'])) $data['pt_timeout'] = $trans->t_timeout;
            $data['pt_status'] = PayTrace::STATUS_INIT;
            $data['pt_pay_status'] = PayTrace::PAY_STATUS_NOPAY;
            $payOrder = $this->createPayOrder($data);
            if(!$payOrder){
                return false;
            }
            $payment = static::getPayment($payOrder->pt_pay_type);
            $payData = [
                'trans_invalid_at' => $payOrder->pt_timeout,
                'trans_start_at' => time(),
                'trans_number' => $payOrder->pt_belong_trans_number,
                'trans_title' => $trans->t_title,
                'trans_total_fee' => $trans->t_fee,
                'trans_detail' => $trans->t_content,
                'trans_product_id' => $trans->t_app_no,
            ];
            $thirdPreOrder = $payment->createOrder($payData, static::$map[$payOrder->pt_pay_type][$payOrder->pt_pre_order_type]);
            if(!$thirdPreOrder){
                list($code, $error) = $payment->getOneError();
                $this->addError($code, $error);
                return false;
            }
            $payOrder->pt_pre_order = $thirdPreOrder['master_data'];
            $payOrder->third_data = [
                'pre_response' => $thirdPreOrder['response']
            ];
            if(false === $payOrder->update(false)){
                $this->addError(Errno::DB_UPDATE_FAIL, Yii::t('app', "修改支付单失败"));
                return false;
            }
            $t->commit();
            return $payOrder;
        } catch (\Exception $e) {
            $t->rollback();
            Yii::error($e);
            $this->addError(Errno::EXCEPTION, Yii::t('app', "创建支付单异常"));
            return false;
        }

    }

    /**
     * [updatePayOrderPayed description]
     * @param  PayTrace $payOrder [description]
     * @param  [type]   $data     [description]
     * - notification: array
     * @return [type]             [description]
     */
    public static function updatePayOrderPayed(PayTrace $payOrder, $data){
        if(!empty($data['notification'])){
            $payOrder->third_data = ['pay_succ_notification' => $data['notification']];
        }
        $payOrder->pt_pay_status = PayTrace::PAY_STATUS_PAYED;
        $payOrder->pt_status = PayTrace::STATUS_PAYED;
        if(false === $payOrder->update(false)){
            $this->addError(Errno::DB_UPDATE_FAIL, Yii::t('app', "修改支付单支失败"));
            return false;
        }
        return $payOrder;
    }

    public static function triggerPayed(PayTrace $payOrder){
        $payOrder->trigger(PayTrace::EVENT_AFTER_PAYED);
    }




    public function createPayOrder($data){
        $payTrace = new PayTrace();
        if(!$payTrace->load($data, '') || !$payTrace->validate()){
            $this->addError('', $this->getOneErrMsg($payTrace));
            return false;
        }
        if(!$payTrace->insert(false)){
            $this->addError(Errno::DB_INSERT_FAIL, Yii::t('app', "插入支付单数据失败"));
            return false;
        }
        return $payTrace;
    }

}
