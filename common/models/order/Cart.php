<?php
namespace common\models\order;

use common\models\Model;
use common\models\goods\ar\GoodsSku;
use common\models\order\ar\CartItem;
use common\models\pay\Currency;
use yii\helpers\ArrayHelper;
use common\models\order\CartModel;

/**
 *
 */
class Cart extends Model
{
    private $_model = null;
    public $user = null;

    
    // 确定要购买的条目，空就是全部购买
    public $confirmBuyedItems = [];

    public function __construct($config = []){
        if(!is_object($config['user'])){
            throw new \InvalidConfigException(Yii::t('app', "Cart中的user属性必须配置"));
        }
        $this->_model = new CartModel();
        parent::__construct($config);
    }

    public function removeGoods(GoodsSku $goodsSku){
        return $this;
    }

    public function increaseGoods(GoodsSku $GoodsSku){
        return $this;
    }

    public function decreaseGoods(GoodsSku $goodsSku){
        return $this;
    }

    public function refreshGoods(GoodsSku $goodsSku,$extra = []){
        return $this;
    }

    public function clear(){
        return $this;
    }

    public function clearInvalid(){
        return $this;
    }

    public function count(){
        return $this;
    }

    public function checkout(){
        return $this;
    }

    public function checkoutAndReport(){
        return $this;
    }

    public function confirmItems($goodsSkuValues){
        return [];
    }

    public function addGoods(GoodsSku $goodsSku, $extra = []){
        $cartItemData = [
            'ct_belong_uid' => $this->user->u_id,
            'ct_object_id' => $goodsSku->g_sku_id,
            'ct_object_value' => $goodsSku->g_sku_value,
            'ct_object_type' => CartItem::OB_TYPE_GOODSSKU,
            'ct_object_status' => CartItem::ITEM_STATUS_VALID,
            'ct_object_data' => $goodsSku->cart_item_info,
            'ct_discount_data' => ArrayHelper::getValue($extra, 'ct_discount_data', ''),
            'ct_object_classification' => ArrayHelper::getValue($extra, 'ct_object_classification', CartItem::NO_CLASS),
            'ct_amount' => ArrayHelper::getValue($extra, 'ct_amount', 1),
            'ct_price' => $goodsSku->g_sku_price,
            // todo fix
            'ct_price_type' => Currency::CNY,
            'ct_object_title' => $goodsSku->cart_item_title
        ];
        $cartItem = $this->_model->createCartItem($cartItemData);
        if(!$cartItem){
            list($code, $error) = $this->_model->getOneError();
            $this->addError($code, $error);
            return false;
        }
        return $this;
    }


}
