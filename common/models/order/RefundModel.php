<?php
namespace common\models\order;

use Yii;
use common\models\staticdata\Errno;
use common\models\order\ar\RfApplication;
use common\models\order\OrderModel;
use common\models\order\ar\Order;

/**
 *
 */
class RefundModel extends Model
{
    // 创建一条退款申请
    public function createRfApplication(Order $order, $rfData = []){

    }


    // 同意退款申请
    public function agreeRfApplication(RfApplication $rfApplication, $replyData = []){

    }


    // 拒绝退款申请
    public function disagreeRfApplication(RfApplication $rfApplication, $replyData = []){

    }

    // 构建退款交易数据
    public function buildRfItemTradeData(){

    }

    // 发出退款请求到支付平台
    public function sendRefundTradeData(){

    }

    
}
