<?php
namespace common\models\order;

use Yii;
use common\models\staticdata\Errno;
use common\models\Model;
use common\models\order\ar\Order;
use common\models\goods\ar\GoodsSku;
use common\models\order\ar\OrderGoods;
use common\models\order\Cart;
use common\models\user\ar\User;
use yii\helpers\ArrayHelper;
use common\models\set\SetModel;
use common\models\price\rules\OrderCouponSlice;
use common\models\price\rules\PriceRule;
/**
 *
 */
class OrderModel extends Model
{
    // 构造订单数据，不依赖数据库
    public function buildOrderData(User $customer, $orderData){
        $coreData = [
            'od_belong_uid' => $customer->u_id,
            'od_number' => '',
        ];
        $finalData = ArrayHelper::merge($orderData, $coreData);
        $order = new Order();
        if(!$order->load($finalData, '') || !$order->validate()){
            $this->addError('', $this->getOneErrMsg($order));
            return false;
        }
        $order->od_number = static::buildOrderNumber();
        foreach($order->goods_sku_data as $goodsSku){
            $orderGoods = new OrderGoods();
            $orderGoods->g_id = $goodsSku->g_id;
            $orderGoods->g_sku_value = $goodsSku->g_sku_value;
            $orderGoods->g_sku_id = $goodsSku->g_sku_id;
            $orderGoods->og_g_sku_price = $goodsSku->g_sku_price;
            $orderGoods->og_g_sku_sale_price = $goodsSku->g_sku_sale_price;
            $orderGoods->og_g_sku_name = $goodsSku->g_sku_name;
            $orderGoods->og_g_sku_value_name = $goodsSku->g_sku_value_name;
            $orderGoods->og_g_sku_data = '';
            $orderGoods->og_discount_data = '';
            $order->addOrderGoods($orderGoods);
        }
        $discountWarn = [];
        try {
            $discountData = $this->buildValidDiscountData($order, $customer, $orderData['discount_data']);
        } catch (\Exception $e) {
            $discountWarn[] = $e->getMessage();
        }
        if($discountWarn){
            $discountData = [];
        }
        $orderPriceItem = $this->buildOrderPriceItem($order, $discountData);
        console($orderPriceItem);



        $discountCandications = $this->buildValidDiscountCandications($order, $customer);
        console($orderData['discount_data'], $discountCandications);


        $discountParams = SetModel::get('global_order_price_discount');
        $userCouponParams = (array)ArrayHelper::getValue($orderData, 'discount_data.use_coupons', []);
        foreach($userCouponParams as $couponItem){
            // 优惠券是应该获取的
            $discountParams[] = [
                'class' => OrderCouponSlice::className(),
                'fullValue' => 500000,
                'sliceValue' => 50000,
                'couponCode' => $couponItem['code']
            ];
        }
        $this->addOrderDiscountCandication($order, $discountParams);
        $order->od_price = $order->caculateOrderPrice();
        return $order;
    }

    public function buildValidDiscountData($order, User $user, $userSelectDiscountData = []){
        $candications = $this->buildValidDiscountCandications($order, $user, false, true);
        // 全局优惠
        $globalOrderDiscount = $candications[PriceRule::TYPE_GLOBAL_ORDER_PRICE_DISCOUNT];
        $selectGlobalOrderDiscount = ArrayHelper::getValue($userSelectDiscountData, PriceRule::TYPE_GLOBAL_ORDER_PRICE_DISCOUNT, []);
        $validDiscount = [];
        foreach($selectGlobalOrderDiscount as $discountItem){
            $targetDiscount = ArrayHelper::getValue($globalOrderDiscount, $discountItem['id'], null);
            if(!$targetDiscount){
                throw new \Exception(Yii::t('app', "所选的的折扣不存在"));
            }
            $validDiscount[] = $targetDiscount;
        }
        console($globalOrderDiscount);
        // 用户可以选择优惠， 如优惠券
        $couponOrderDiscount = $candications[PriceRule::TYPE_USER_COUPON_PRICE_DISCOUNT];
        $selectCouponOrderDiscount = ArrayHelper::getValue($userSelectDiscountData, PriceRule::TYPE_USER_COUPON_PRICE_DISCOUNT, []);
        foreach($selectCouponOrderDiscount as $couponItem){
            $targetDiscount = ArrayHelper::getValue($couponOrderDiscount, $couponItem['id'], null);
            if(!$targetDiscount){
                throw new \Exception(Yii::t('app', "优惠码不存在"));
            }
            $validDiscount[] = $targetDiscount;
        }
        // 互斥性检查
        foreach($validDiscount as $targetDiscount){
            $targetDiscount->checkExist && $targetDiscount->checkExistRule($validDiscount);
        }
        return $validDiscount;
    }

    public function buildValidDiscountCandications(Order $order, User $user, $appendCantUse = true, $returnObject = false){

        $price = static::caculateOrderPrice($order);
        // 全局规则
        $discountItems = SetModel::get('global_order_price_discount');
        $candications = [
            PriceRule::TYPE_GLOBAL_ORDER_PRICE_DISCOUNT => [],
            PriceRule::TYPE_USER_COUPON_PRICE_DISCOUNT => []
        ];
        foreach($discountItems as $discountDef){
            $discountDef['order'] = $order;
            $discountDef['originPrice'] = $price;
            $discount = Yii::createObject($discountDef);
            if(!$appendCantUse && !$discount->checkCanUse()){
                continue;
            }
            $discountData = $returnObject ? $discount : $discount->toArray();
            $candications[PriceRule::TYPE_GLOBAL_ORDER_PRICE_DISCOUNT][$discountData['id']] = $discountData;
        }
        // 优惠码
        $couponItems = SetModel::get('user_coupon_faker');
        foreach($couponItems as $coupon){
            $discountDef = [
                'class' => OrderCouponSlice::className(),
                'order' => $order,
                'id' => $coupon['oc_code'],
                'originPrice' => $price,
                'fullValue' => $coupon['oc_params']['full_value'],
                'sliceValue' => $coupon['oc_params']['slice_value'],
                'beginAt' => $coupon['oc_begin'],
                'endAt' => $coupon['oc_end'],
                'couponCode' => $coupon['oc_code']
            ];
            $discount = Yii::createObject($discountDef);
            if(!$appendCantUse && !$discount->checkCanUse()){
                continue;
            }
            $discountData = $returnObject ? $discount : $discount->toArray();
            $candications[PriceRule::TYPE_USER_COUPON_PRICE_DISCOUNT][$discountData['couponCode']] = $discountData;
        }
        return $candications;
    }

    public static function buildOrderPriceItem(Order $order, $discountData = []){
        $priceItem = [];
        foreach($discountData as $discount){
            $priceItem[] = [
                'fee' => $discount->newPrice - $discount->originPrice,
                'description' => $discount->description,
                'type' => $discount->type
            ];
        }
        $priceItem[] = [
            'fee' => $discount->originPrice,
            'description' => Yii::t('app', '商品金额'),
            'type' => 'goods_total_fee'
        ];
        return $priceItem;
    }



    public static function caculateOrderPrice(Order $order){
        $orderGoods = $order->order_goods;
        $orderFinalPrice = 0;
        foreach($orderGoods as $oneOrderGoods){
            $orderFinalPrice += $oneOrderGoods->og_g_sku_price;
        }
        return $orderFinalPrice;
    }

    public function addOrderDiscountCandication(Order $order, $discountParams = []){
        $price = static::caculateOrderPrice($order);
        foreach($discountParams as $discountDef){
            $discountDef['order'] = $order;
            $discountDef['originPrice'] = $price;
            $discount = Yii::createObject($discountDef);
            if($discount->checkCanUse($price)){
                $order->addOrderDiscountCandication($discount->toArray());
            }
        }
        return $order;
    }



    // 立即购买
    public function createOrderAtOnce(GoodsSku $goodsSku,  User $customer, $orderData = [], $faker = false){
        $coreData = [
            'od_belong_uid' => $customer->u_id,
            'od_number' => '',
            'od_belong_storage' => $goodsSku->goods->g_storage
        ];
        $finalData = ArrayHelper::merge($orderData, $coreData);
        $order = $this->createOrder($finalData);
        if(!$order){
            return false;
        }
        $itemData = [
            'goods_sku' => $goodsSku,
        ];
        $orderGoods = $this->createOrderItem($order, $itemData);
        if(!$orderGoods){
            return false;
        }
        console($order->toArray(), $orderGoods->toArray());
    }

    public static function buildOrderNumber(){
        list($time, $millsecond) = explode('.', microtime(true));
        $string = sprintf("OD%s%04d", date("HYisdm", $time), $millsecond);
        return $string;
    }



    // 购物车购买
    public function createOrderFromCart(Cart $cart, $extraData = [], $faker = false){

    }



    // 创建订单
    public function createOrder(Order $order){
        if(!$order->insert(false)){
            $this->addError(Errno::DB_INSERT_FAIL, Yii::t('app', "插入订单失败"));
            return false;
        }
        return $order;
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
        if(empty($itemData['goods_sku']) || is_object($itemData)){
            $this->addError("", Yii::t('app', "goods_sku数据不能为空,且必须时GoodsSku对象"));
            return false;
        }
        $goodsSku = $itemData['goods_sku'];

        $orderGoods = new OrderGoods();

        $orderGoods->od_id = $order->od_id;
        $orderGoods->g_id = $goodsSku->g_id;
        $orderGoods->g_sku_value = $goodsSku->g_sku_value;
        $orderGoods->g_sku_id = $goodsSku->g_sku_id;
        $orderGoods->og_g_sku_price = $goodsSku->g_sku_price;
        $orderGoods->og_g_sku_sale_price = $goodsSku->g_sku_sale_price;
        $orderGoods->og_g_sku_name = $goodsSku->g_sku_name;
        $orderGoods->og_g_sku_value_name = $goodsSku->g_sku_value_name;
        $orderGoods->og_g_sku_data = '';
        $orderGoods->og_discount_data = '';
        if(!$orderGoods->insert(false)){
            $this->addError(Errno::DB_INSERT_FAIL, Yii::t('app', "创建订单商品失败"));
            return false;
        }
        return $orderGoods;
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
