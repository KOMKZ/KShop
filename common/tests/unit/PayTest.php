<?php
namespace common\tests;
use common\models\pay\PayModel;
use common\models\pay\payment\Wxpay;


class PayTest extends \Codeception\Test\Unit
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
    // tests
    public function testCreateWxpayOrder()
    {
        return ;
        $wxpay = PayModel::getPayment(Wxpay::NAME);
        $data = [
            'trans_body' => '测试数据内容说明',
            'trans_detail' => '测试数据内容详细',
            'trans_total_fee' => 1,
            'trans_start_at' => time(),
            'trans_invalid_at' => time() + 3600,
        ];
        $num1 = 'ceshi' . mt_rand(111111111,999999999);
        $data['trans_number'] = $num1;
        $data['trans_module_number'] = 'ceshi' . mt_rand(111111111,999999999);
        $result = $wxpay->createOrder($data, Wxpay::MODE_NATIVE);
        $this->assertEquals(true, !empty($result));


        $num2 = 'ceshi' . mt_rand(111111111,999999999);
        $data['trans_number'] = $num2;
        $data['trans_module_number'] = 'ceshi' . mt_rand(111111111,999999999);
        $result = $wxpay->createOrder($data, Wxpay::MODE_APP);
        $this->assertEquals(true, !empty($result));


        $result = $wxpay->queryOrder(['trans_number' => $num1]);
        $this->assertEquals(true, !empty($result));


        $result = $wxpay->checkOrderIsPayed(['trans_number' => $num1]);
        $this->assertEquals(false, $result);
    }

    public function testCloseOrder(){
        return ;
        // $wxpay = PayModel::getPayment(Wxpay::NAME);
        // $data = [
        //     'trans_body' => '测试数据内容说明',
        //     'trans_detail' => '测试数据内容详细',
        //     'trans_total_fee' => 1,
        //     'trans_start_at' => time(),
        //     'trans_invalid_at' => time() + 3600,
        // ];
        // $num1 = 'ceshi' . mt_rand(111111111,999999999);
        // $data['trans_number'] = $num1;
        // $data['trans_module_number'] = 'ceshi' . mt_rand(111111111,999999999);
        // $result = $wxpay->createOrder($data, Wxpay::MODE_NATIVE);
        // console($num1, $result['master_data']);

        $num = "ceshi720415275";
        $wxpay = PayModel::getPayment(Wxpay::NAME);
        $result = $wxpay->closeOrder(['trans_number' => $num]);
        return $this->assertEquals(true, !empty($result));

    }


    public function testQueryRefund(){
        // 需要提供一个已经退款的单号
        return ;
        $num = 'ceshi3208488942';
        $wxpay = PayModel::getPayment(Wxpay::NAME);
        $result = $wxpay->queryRefund(['trans_number' => $num]);
        $this->assertEquals(true, !empty($result));
        $result = $wxpay->checkOrderIsRefunded(['trans_number' => $num]);
        return $this->assertEquals(true, !empty($result));
    }
    public function testCreate(){
        $wxpay = PayModel::getPayment(Wxpay::NAME);
        $data = [
            'trans_body' => '测试数据内容说明',
            'trans_detail' => '测试数据内容详细',
            'trans_total_fee' => 1,
            'trans_start_at' => time(),
            'trans_invalid_at' => time() + 3600,
        ];
        $num1 = 'ceshi' . mt_rand(111111111,999999999);
        $data['trans_number'] = $num1;
        $data['trans_module_number'] = 'ceshi' . mt_rand(111111111,999999999);
        $result = $wxpay->createOrder($data, Wxpay::MODE_NATIVE);
        console($num1, $result['master_data']);
    }
    public function testRefund(){
        return ;
        // $wxpay = PayModel::getPayment(Wxpay::NAME);
        // $data = [
        //     'trans_body' => '测试数据内容说明',
        //     'trans_detail' => '测试数据内容详细',
        //     'trans_total_fee' => 1,
        //     'trans_start_at' => time(),
        //     'trans_invalid_at' => time() + 3600,
        // ];
        // $num1 = 'ceshi' . mt_rand(111111111,999999999);
        // $data['trans_number'] = $num1;
        // $data['trans_module_number'] = 'ceshi' . mt_rand(111111111,999999999);
        // $result = $wxpay->createOrder($data, Wxpay::MODE_NATIVE);
        // console($num1, $result['master_data']);

        // 需要提供一个已经退款的单号

        $num = 'ceshi320848894';
        $wxpay = PayModel::getPayment(Wxpay::NAME);
        $result = $wxpay->createRefund([
            'trans_number' => $num,
            'trans_total_fee' => 1,
            'trans_refund_fee' => 1,
            'trans_refund_number' => $num,
        ]);
        return $this->assertEquals(true, !empty($result));

    }

}
