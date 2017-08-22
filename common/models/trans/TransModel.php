<?php
namespace common\models\trans;

use common\models\Model;
use common\models\staticdata\Errno;
use common\models\trans\ar\Transaction;
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

    protected static function buildTradeNumber(){
        list($time, $millsecond) = explode('.', microtime(true));
        $string = sprintf("TR%s%04d", date("HYisdm", $time), $millsecond);
        return $string;
    }

}
