<?php
namespace common\tests;
use common\models\pay\PayModel;
use common\models\pay\payment\Alipay;


class WxpayTest extends \Codeception\Test\Unit
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
    public function testRefund(){
        return ;
        $num1 = 'ceshi388717177';
        $data = [
            'trans_number' => $num1,
            'trans_total_fee' => 1,
            'trans_refund_fee' => 1,
            'trans_refund_number' => $num1,
            'trans_refund_reasons' => "用户退款",
        ];
        $payment = PayModel::getPayment(Alipay::NAME);
        $result = $payment->createRefund($data);
        // console($result, payment->getOneError());
        $this->assertEquals(true, !empty($result));
    }
    public function testCloseOrder(){
        return ;
        $num1 = 'ceshi209683313';
        $payment = PayModel::getPayment(Alipay::NAME);
        $result = $payment->closeOrder(['trans_number' => $num1]);
        $this->assertEquals(true, !empty($result));
    }
    public function testQueryRefund(){
        // return ;
        $num1 = 'ceshi388717177';
        $payment = PayModel::getPayment(Alipay::NAME);
        $result = $payment->queryRefund(['trans_number' => $num1]);;
        console($result, $payment->getOneError());
    }
    public function testQueryOrder(){
        return ;
        $num1 = 'ceshi209683313';
        $payment = PayModel::getPayment(Alipay::NAME);
        $result = $payment->queryOrder(['trans_number' => $num1]);
        $this->assertEquals(true, !empty($result));
        $this->assertEquals(false, $payment->checkOrderIsPayed($result));
    }
    public function testCreateUrlOrder(){
        return ;
        $payment = PayModel::getPayment(Alipay::NAME);
        $data = [
            'trans_title' => '测试数据内容说明',
            'trans_detail' => '测试数据内容详细',
            'trans_total_fee' => 1,
            'trans_start_at' => time(),
            'trans_invalid_at' => time() + 3600,
        ];
        $num1 = 'ceshi' . mt_rand(111111111,999999999);
        $data['trans_number'] = $num1;
        $data['trans_module_number'] = 'ceshi' . mt_rand(111111111,999999999);
        $result = $payment->createOrder($data, Alipay::MODE_URL);
        // console($num1, $result);
        $this->assertEquals(true, empty($result));
    }
    public function testCreateAppOrder(){
        return ;
        $payment = PayModel::getPayment(Alipay::NAME);
        $data = [
            'trans_title' => '测试数据内容说明',
            'trans_detail' => '测试数据内容详细',
            'trans_total_fee' => 1,
            'trans_start_at' => time(),
            'trans_invalid_at' => time() + 3600,
        ];
        $num1 = 'ceshi' . mt_rand(111111111,999999999);
        $data['trans_number'] = $num1;
        $data['trans_module_number'] = 'ceshi' . mt_rand(111111111,999999999);
        $result = $payment->createOrder($data, Alipay::MODE_APP);
        console($result);
    }
}
