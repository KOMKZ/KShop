<?php
namespace common\tests;
use Yii;
use common\models\trans\TransModel;
use common\models\trans\ar\Transaction;
use common\models\pay\ar\PayTrace;
use common\models\pay\payment\Alipay;
use common\models\pay\payment\Wxpay;
use common\models\pay\PayModel;


class TransTest extends \Codeception\Test\Unit
{

    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function debug($data){
        console($data);
    }

    public function testCreate(){
        return ;
        Yii::$app->db->beginTransaction();
        $data = [
            't_status' => Transaction::STATUS_INIT,
            't_type' => Transaction::TYPE_CONSUME,
            't_module' => Transaction::MODULE_ORDER,
            't_app_no' => mt_rand(11111,99999),
            't_belong_uid' => 1,
            't_create_uid' => 2,
            't_fee' => 1,
            't_title' => "测试产品"
        ];
        $transModel = new TransModel();
        $trans = $transModel->createTrans($data);
        if(!$trans){
            $this->debug($transModel->getOneError());
        }
        console($trans->toArray());
    }

    public function testCreatePayOrder(){
        // return true;
        // Yii::$app->db->beginTransaction();
        // todo fix 重复创建
        $data = [
            't_status' => Transaction::STATUS_INIT,
            't_type' => Transaction::TYPE_CONSUME,
            't_module' => Transaction::MODULE_ORDER,
            't_app_no' => mt_rand(11111,99999),
            't_belong_uid' => 1,
            't_create_uid' => 2,
            't_fee' => 1,
            't_title' => "测试产品",
            "t_content" => "",
        ];
        $transModel = new TransModel();
        $trans = $transModel->createTrans($data);
        if(!$trans){
            $this->debug($transModel->getOneError());
        }
        $payModel = new PayModel();
        $payData = [
            'pt_pay_type' => Wxpay::NAME,
            'pt_pre_order_type' => PayTrace::TYPE_URL,
            'pt_timeout' => $trans->t_timeout,
        ];
        $payOrder = $payModel->createPreOrder($payData, $trans);
        if(!$payOrder){
            $this->debug($payModel->getOneError());
        }
        console($payOrder->toArray());
    }
}
