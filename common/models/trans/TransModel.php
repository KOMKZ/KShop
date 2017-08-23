<?php
namespace common\models\trans;

use common\models\Model;
use common\models\staticdata\Errno;
use common\models\trans\ar\Transaction;
use common\models\trans\query\TransactionQuery;
/**
 *
 */
class TransModel extends Model
{

    public function createTrans($data){
        $trans = new Transaction();
        if(!$trans->load($data, '') || !$trans->validate()){
            $this->addError("", $this->getOneErrMsg($trans));
            return false;
        }
        $trans->t_number = static::buildTradeNumber();
        if(!$trans->insert(false)){
            $this->addError(Errno::DB_INSERT_FAIL, Yii::t('app', "插入交易失败"));
            return false;
        }
        return $trans;
    }

    public function createInitTrans($data){
        $data['t_status'] = Transaction::STATUS_INIT;
        $data['t_pay_status'] = Transaction::PAY_STATUS_NOPAY;
        return $this->createTrans($data);
    }

    public static function handleReceivePayedEvent($event){
        $payOrder = $event->sender;
        $trans = TransactionQuery::findConsume()
                                       ->andWhere(['=', 't_number', $payOrder->pt_belong_trans_number])
                                       ->one();
        if(Transaction::PAY_STATUS_PAYED == $trans->t_pay_status){
            // 该交易已经支付 记录一下日志即可 todo
            // Yii::info(["通知得到的数据但是交易已经在平台处于支付状态", $payOrder->toArray()], "trans_payed_repeated")
            return ;
        }
        // 修改交易数据
        $trans->t_succ_pay_type = $payOrder->pt_pay_type;
        $trans->t_pay_status = Transaction::PAY_STATUS_PAYED;
        $trans->t_status = Transaction::STATUS_PAYED;
        $trans->t_pay_at = time();
        if(false === $trans->update(false)){
            throw new \Exception(Yii::t('app', "更改交易失败"));
        }
        static::triggerTransPayed($trans);
    }

    public static function triggerTransPayed(Transaction $trans){
        $trans->trigger(Transaction::EVENT_AFTER_PAYED);
    }

    protected static function buildTradeNumber(){
        list($time, $millsecond) = explode('.', microtime(true));
        $string = sprintf("TR%s%04d", date("HYisdm", $time), $millsecond);
        return $string;
    }

}
