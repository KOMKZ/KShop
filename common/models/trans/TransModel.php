<?php
namespace common\models\trans;

use common\models\Model;
use common\models\staticdata\Errno;
use common\models\trans\ar\Transaction;
use common\models\trans\query\TransactionQuery;
use common\models\trans\event\AfterPayedEvent;
use common\models\user\query\UserQuery;
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

    /**
     * 支付单成功支付时关联交易响应处理
     * @param  [type] $event [description]
     * @return [type]        [description]
     */
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
        // 查找交易所属用户，分发给其他模块
        $event = new AfterPayedEvent();
        $event->belongUser = UserQuery::findActive()->andWhere(['=', 'u_id', $trans->t_belong_uid])->one();
        $event->payOrder = $payOrder;
        static::triggerTransPayed($trans, $event);
    }

    public static function triggerTransPayed(Transaction $trans, $event = null){
        $trans->trigger(Transaction::EVENT_AFTER_PAYED, $event);
    }

    protected static function buildTradeNumber(){
        list($time, $millsecond) = explode('.', microtime(true));
        $string = sprintf("TR%s%04d", date("HYisdm", $time), $millsecond);
        return $string;
    }

}
