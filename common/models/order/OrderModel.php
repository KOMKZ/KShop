<?php
namespace common\models\order;

use Yii;
use common\models\staticdata\Errno;
use common\models\Model;
use common\models\order\ar\Order;
use common\models\goods\ar\GoodsSku;
use common\models\order\Cart;
/**
 *
 */
class OrderModel extends Model
{
    // 立即购买
    public function createOrderAtOnce(GoodsSku $goodsSku, $extraData = [], $faker = false){

    }

    // 购物车购买
    public function createOrderFromCart(Cart $cart, $extraData = [], $faker = false){

    }

    // 创建初始化阶段的订单
    public function createInitOrder($data, $faker = false){
        $data['od_pay_status'] = Order::PAY_STATUS_NOPAY;
        $data['od_status'] = Order::STATUS_INIT;
        $data['od_comment_status'] = Order::COMMENT_STATUS_NO_C;
        $data['od_refund_status'] = Order::RF_STATUS_NO_RF;
        $data['od_logistics_status'] = Order::LG_STATUS_INIT;

    }

    // 创建订单
    public function createOrder($data, $faker){

    }

    // 校验商品数据
    public function validateGoodsData($goodsData){

    }

    // 商品按仓库分类
    public function classifyGoodsByStorage($goodsData){

    }

    // 创建子订单
    public function createChildOrder(Order $masterOrder, $childGoodsData){

    }

    // 创建订单商品数据，extra中存储折扣和优惠信息的选择
    public function createOrderItem(Order $order, $itemData){

    }

    // 同楼上，创建多条
    public function createOrderItems(Order $order, $itemsData){

    }

    // 计算订单的最终价格， extra中存储折扣和优惠信息
    public function caculateActivePrice(Order $order, $extra = []){

    }

    // 检查订单是否依旧有效
    public function checkOrderIsValid(Order $order){

    }


    // 构建订单的支付数据
    public function buildOrderPayData(Order $order, $payData){
        // 创建交易
        // 创建支付单
    }

    // 取消订单
    public function cancelOrder(Order $order){

    }

    // 确认收货
    public function confirmReceiveGoods(Order $order){

    }

    // 确认完成
    public function confirmCompleteOrder(Order $order){

    }

    // 更新订单
    public function updateOrder(Order $order, $data = []){

    }

    public static function handleTransPayed($event){
        $trans = $event->sender;
        $belongUser = $event->belongUser;
        $payOrder = $event->payOrder;
    }



}
