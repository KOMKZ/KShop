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
use common\models\user\query\UserReceAddrQuery;
use common\models\order\ar\OrderDiscount;
use common\models\order\ar\OrderReceiverAddr;
/**
 *
 */
class OrderModel extends Model
{
    /**
     *
     * @param  User   $customer  [description]
     * @param  [type] $orderData [description]
     * - goods_sku_data: array, required 购买商品数据
     *   - sku: \common\models\goods\ar\GoodsSku required 商品sku对象
     *   - number: integer, optional default:1 购买sku的数量
     * - receiver_addr_id: integer 收获地址id
     * 如果没有传入收获地址，则会使用默认收获地址，如果没有默认收获地址，则会产生一个警告，对于提交订单的话怎会产生一个错误
     * - discount_data: array 用户选择的折扣数据
     *   - user_coupon_price_discount: array 用户选择的优惠码折扣数据
     *      - id: string 折扣数据的唯一编号， 即优惠码编号
     *   - global_order_price_discount: array 用户选择的全局优惠数据
     *      - id: string 折扣数据的唯一编号
     * @return [type]            [description]
     */
    public function createOrderPreData(User $customer, $orderData){
        // 构造订单基础数据
        $order = $this->createOrderData($customer, $orderData);
        if(!$order){
            return false;
        }
        // 构造订单商品数据
        $orderGoodsResult = $this->createOrderGoodsData($order, ArrayHelper::getValue($orderData, 'goods_sku_data'));
        if(empty($orderGoodsResult)){
            $this->addError('', Yii::t('app', "没有购买任何商品"));
            return false;
        }
        $order->od_goods = $orderGoodsResult;

        // 构造订单有效折扣数据
        $order->od_valid_discount_data = $this->buildValidDiscountCandications($order, $customer);

        // 构造订单折扣数据
        try {
            $discountData = $this->createValidDiscountData($order, $customer, ArrayHelper::getValue($orderData, 'discount_data'));
        } catch (\Exception $e) {
            array_push($order->od_warn, $e->getMessage());
        }
        if(!empty($order->od_warn)){
            $discountData = [];
        }
        $order->od_discount_data = $discountData;
        // 构造收货人数据
        $receiverAddr = $this->buildOrderReceiverAddrData($customer, ArrayHelper::getValue($orderData, 'receiver_addr_id'));
        if(!$receiverAddr){
            array_push($order->od_warn, Yii::t('app', "请制定收货地址或者设置一个默认地址"));
        }else{
            $order->od_rece_addr = $receiverAddr;
        }

        // 构造物流数据
        $hasExpressDiscount = !empty(ArrayHelper::getValue($discountData, 'order_exempt_express_fee', null));
        $orderExpress = static::buildOrderExpressData($order, $hasExpressDiscount);
        $order->od_express = $orderExpress;

        // 构造价格清单数据
        $orderPriceItems = $this->buildOrderPriceItem($order, $discountData);
        $orderPriceItemIndex = ArrayHelper::index($orderPriceItems, 'id');
        $finalPrice = ArrayHelper::getValue($orderPriceItemIndex, 'order_final_fee', null);
        $originPrice = ArrayHelper::getValue($orderPriceItemIndex, $order->od_number, null);
        if(in_array(null, [$finalPrice, $originPrice])){
            $this->addError('', Yii::t('app', "订单数据错误"));
            return false;
        }
        $order->od_price_items = $orderPriceItems;

        // 设置订单价格
        $order->od_price = $finalPrice['fee'];
        $order->od_origin_price = $originPrice['fee'];
        return $order;
    }
    protected function buildOrderReceiverAddrData(User $customer, $receiverAddrId = null){
        $receAddr = null;
        if($receiverAddrId){
            $receAddr = UserReceAddrQuery::find()
                                          ->where(['rece_belong_uid' => $customer->u_id, 'rece_addr_id' => $receiverAddrId])
                                          ->one();
        }
        $receAddr = UserReceAddrQuery::find()
                                      ->where(['rece_belong_uid' => $customer->u_id, 'rece_default_addr' => 'yes'])
                                      ->one();
        return $receAddr;
    }

    public function createOrder(User $customer, $orderData){
        $order = $this->createOrderPreData($customer, $orderData);
        // 插入订单基本数据
        if(!$order->insert(false)){
            $this->addError(Errno::DB_INSERT_FAIL, Yii::t('app', "创建订单失败"));
            return false;
        }
        // 插入订单商品数据
        foreach($order->od_goods as $orderGoods){
            $orderGoods->od_id = $order->od_id;
            if(!$orderGoods->insert(false)){
                $this->addError(Errno::DB_INSERT_FAIL, Yii::t('app', '插入订单商品失败'));
                return false;
            }
        }
        // 插入订单折扣数据
        foreach($order->od_discount_data as $discountItem){
            $data = [
                'od_discount_data' => json_encode($discountItem),
                'od_discount_data_id' => $discountItem['id'],
                'od_discount_slice_value' =>  (int)$discountItem['sliceValue'],
                'od_discount_created_at' => time(),
                'od_discount_type' => $discountItem['type'],
                'od_discount_class' => $discountItem['class'],
                'od_id' => $order->od_id,
                'od_discount_description' => $discountItem['description']
            ];
            if(!$this->createOrderDiscount($data)){
                $this->addError(Errno::DB_INSERT_FAIL, Yii::t('app', "插入订单折扣数据失败"));
                return false;
            }
        }
        // 插入订单收货数据
        $receAddrData = $order->od_rece_addr->toArray();
        unset(
            $receAddrData['rece_status'],
            $receAddrData['rece_created_at'],
            $receAddrData['rece_default_addr']
        );
        $receAddrData['od_id'] = $order->od_id;
        if(!$this->createOrderReceiverAddr($receAddrData)){
            $this->addError(Errno::DB_INSERT_FAIL, Yii::t('app', "插入订单收货数据失败"));
            return false;
        }

    }
    protected function createOrderDiscount($data){
        return Yii::$app->db->createCommand()->insert(OrderDiscount::tableName(), $data)->execute();
    }
    protected function createOrderReceiverAddr($data){
        return Yii::$app->db->createCommand()->insert(OrderReceiverAddr::tableName(), $data)->execute();
    }
    protected function createOrderData(User $customer, $orderData){
        // 构建订单基础数据
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
        return $order;
    }

    protected function createOrderGoodsData(Order $order, $goodsData = []){
        $orderGoodsResult = [];
        foreach($goodsData as $skuData){
            $goodsSku = $skuData['sku'];
            $orderGoods = new OrderGoods();
            $orderGoods->g_id = $goodsSku->g_id;
            $orderGoods->g_sku_value = $goodsSku->g_sku_value;
            $orderGoods->og_g_bug_number = ArrayHelper::getValue($skuData, 'number', 1);
            $orderGoods->og_g_sku_total_price = $goodsSku->g_sku_price * $orderGoods->og_g_bug_number;
            $orderGoods->g_sku_id = $goodsSku->g_sku_id;
            $orderGoods->og_g_sku_price = $goodsSku->g_sku_price;
            $orderGoods->og_g_sku_sale_price = $goodsSku->g_sku_sale_price;
            $orderGoods->og_g_sku_name = $goodsSku->g_sku_name;
            $orderGoods->og_g_sku_value_name = $goodsSku->g_sku_value_name;
            $orderGoods->og_g_sku_data = '';
            $orderGoods->og_discount_data = '';
            $orderGoodsResult[] = $orderGoods;
        }
        return $orderGoodsResult;
    }



    // 根据用户所选数据构造最终有效的折扣数据
    public function createValidDiscountData($order, User $user, $userSelectDiscountData = []){
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
            $validDiscount[$targetDiscount->id] = $targetDiscount;
        }
        $expressDiscount = ArrayHelper::getValue($globalOrderDiscount, 'order_exempt_express_fee', null);
        if($expressDiscount){
            $validDiscount[$expressDiscount->id] = $expressDiscount;
        }
        // 用户可以选择优惠， 如优惠券
        $couponOrderDiscount = $candications[PriceRule::TYPE_USER_COUPON_PRICE_DISCOUNT];
        $selectCouponOrderDiscount = ArrayHelper::getValue($userSelectDiscountData, PriceRule::TYPE_USER_COUPON_PRICE_DISCOUNT, []);
        foreach($selectCouponOrderDiscount as $couponItem){
            $targetDiscount = ArrayHelper::getValue($couponOrderDiscount, $couponItem['id'], null);
            if(!$targetDiscount){
                throw new \Exception(Yii::t('app', "优惠码不存在"));
            }
            $validDiscount[$targetDiscount->id] = $targetDiscount;
        }
        // 互斥性检查
        foreach($validDiscount as $targetDiscount){
            $targetDiscount->checkExist && $targetDiscount->checkExistRule($validDiscount);
        }
        return $validDiscount;
    }

    // 构造用户有效的折扣数据
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

    // 构造价格清单数据
    public static function buildOrderPriceItem(Order $order, $discountData = []){
        $price = static::caculateOrderPrice($order);
        $priceItems = [];
        foreach($discountData as $discount){
            $priceItems[] = [
                'fee' => $discount->newPrice - $discount->originPrice,
                'description' => $discount->description,
                'type' => $discount->type,
                'id' => $discount->id,
            ];
        }
        $priceItems[] = [
            'fee' => $price,
            'description' => Yii::t('app', '商品金额'),
            'type' => 'goods_total_fee',
            'id' => $order->od_number
        ];
        $orderExpress = $order->od_express;
        if($orderExpress->express_fee > 0){
            $priceItems[] = [
                'fee' => $orderExpress->express_fee,
                'description' => $orderExpress->express_title,
                'type' => 'express_fee',
                'id' => $orderExpress->express_number
            ];
        }
        $finalFee = 0;
        foreach($priceItems as $priceItem){
            $finalFee += $priceItem['fee'];
        }
        $priceItems[] = [
            'fee' => $finalFee,
            'description' => Yii::t('app', "最终价格"),
            'type' => 'order_final_fee',
            'id' => 'order_final_fee'
        ];

        return $priceItems;
    }

    public static function buildOrderExpressData(Order $order, $hasExpressDiscount = false){
        $expressData = [
            'express_fee' => $hasExpressDiscount ? 0 : 1200,
            'express_title' => "快递费",
        ];
        return (object)$expressData;
    }



    public static function caculateOrderPrice(Order $order){
        $orderGoods = $order->od_goods;
        $orderFinalPrice = 0;
        foreach($orderGoods as $oneOrderGoods){
            $orderFinalPrice += $oneOrderGoods->og_g_sku_total_price;
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
