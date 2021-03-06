<?php
namespace common\models\order\ar;

use Yii;
use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
use common\models\order\OrderModel;
use yii\behaviors\TimestampBehavior;
use common\validators\TypeValidator;
use common\models\goods\ar\GoodsSku;
use common\models\order\ar\OrderGoods;
use common\models\order\ar\OrderDiscount;
/**
 *
 */
class Order extends ActiveRecord
{
    const EVENT_AFTER_PAYED = 'event_order_payed';

    const OD_TYPE_GOODS = 'goods';

    const PAY_STATUS_NOPAY = 'nopay';
    const PAY_STATUS_PAYED = 'payed';

    const COMMENT_STATUS_NOCOMMENT = 'nocomment';
    const COMMENT_STATUS_COMMENTED = 'commented';

    const RF_STATUS_NORF = 'norf';
    const RF_STATUS_CUSTOMER_SUBMIT = 'c_submit';

    const STATUS_SUBMIT = 'submit';
    CONST STATUS_C_PAYED = 'c_payed';

    const LG_STATUS_INIT = 'init';

    const MODE_FULL_ONLINE_PAY = 'fullpay';

    // public $goods_sku_data;

    private $_orderGoods = [];
    private $_discountCandication = [];
    private $_orderExpress = [];
    private $_orderPriceItems = [];
    private $_orderDiscountData = [];
    private $_odExpress = null;
    private $_odReceAddr = null;
    public $od_warn = [];
    public $od_valid_discount_data = [];

    public function getOd_rece_addr(){
        return $this->_odReceAddr;
    }

    public function setOd_rece_addr($value){
        $this->_odReceAddr = $value;
    }

    public function getOd_price_items(){
        return $this->_orderPriceItems;
    }

    public function setOd_price_items($value){
        $this->_orderPriceItems = $value;
    }

    public function getOd_discount_data(){
        if(!empty($this->_orderDiscountData)){
            return $this->_orderDiscountData;
        }
        return $this->hasMany(OrderDiscount::className(), ['od_id' => 'od_id']);
    }

    public function setOd_discount_data($value){
        $this->_orderDiscountData = $value;
    }

    public function getOd_express(){
        if(!empty($this->_odExpress)){
            return $this->_odExpress;
        }
        return $this->hasOne(OrderExpress::className(), ['od_id' => 'od_id']);
    }

    public function setOd_express($value){
        $this->_odExpress = $value;
    }

    public function fields(){
        $fields = parent::fields();
        return array_merge($fields, [
            'od_goods',
            'od_rece_addr',
            'od_express',
            'od_valid_discount_data',
            'od_discount_data',
            'od_price_items'
        ]);
    }

    public function getOd_goods(){
        if(!empty($this->_orderGoods)){
            return $this->_orderGoods;
        }
        return $this->hasMany(OrderGoods::className(), ['od_id' => 'od_id']);
    }



    public function setOd_goods($value){
        $this->_orderGoods = $value;
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'od_created_at',
                'updatedAtAttribute' => 'od_updated_at'
            ]
        ];
    }

    public static function tableName(){
        return "{{%order}}";
    }

    public function getOrigin_price(){

    }



    public function addOrderDiscountCandication($discount){
        // todo 验证
        array_push($this->_discountCandication, $discount);
    }

    public function caculateOrderPrice(){
        $originPrice = OrderModel::caculateOrderPrice($this);

        console($this->_discountCandication);
    }
    public function setExpress($value){
        $this->_orderExpress = $value;
    }
    public function getExpress(){
        return $this->_orderExpress;
    }

    // public function validateGoods_sku_data($attr){
    //     $skuData = $this->$attr;
    //     foreach($skuData as $skuData){
    //         if($skuData['sku'] instanceof GoodsSku)continue;
    //         $this->addError('', Yii::t('app', "goods_sku_data中每个元素都必须是GoodsSku"));
    //     }
    // }
    public function scenarios(){
        return [
            'default' => [
                'od_type', 'od_title', 'od_pay_status', 'od_comment_status', 'od_refund_status', 'od_status', 'od_belong_storage', 'od_logistics_status', 'od_pay_mode', 'od_belong_uid', 'od_operator_uid', 'od_pid', 'od_payed_at', 'od_invalid_at', 'od_number',
            ],
            'update' => [
                'od_type', 'od_title', 'od_pay_status', 'od_comment_status', 'od_refund_status', 'od_status', 'od_belong_storage', 'od_logistics_status', 'od_pay_mode', 'od_belong_uid', 'od_operator_uid', 'od_pid', 'od_payed_at', 'od_invalid_at', 'od_number',
            ]
        ];
    }
    public function rules(){
        return [
            ['od_type', 'default', 'value' => self::OD_TYPE_GOODS]
            ,['od_type', 'in', 'range' => ConstMap::getConst('od_type', true)]
            // ,['goods_sku_data', 'required']
            // ,['goods_sku_data', TypeValidator::className(), 'expectType' => 'array']
            // ,['goods_sku_data', 'validateGoods_sku_data']

            ,['od_title', 'default', 'value' => '']

            ,['od_pay_status', 'default', 'value' => self::PAY_STATUS_NOPAY]
            ,['od_pay_status', 'in', 'range' => ConstMap::getConst('od_pay_status', true)]

            ,['od_comment_status', 'default', 'value' => self::COMMENT_STATUS_NOCOMMENT]
            ,['od_comment_status', 'in', 'range' => ConstMap::getConst('od_comment_status', true)]

            ,['od_refund_status', 'default', 'value' => self::RF_STATUS_NORF]
            ,['od_refund_status', 'in', 'range' => ConstMap::getConst('od_refund_status', true)]

            ,['od_status', 'default', 'value' => self::STATUS_SUBMIT]
            ,['od_status', 'in', 'range' => ConstMap::getConst('od_status', true)]

            ,['od_belong_storage', 'default', 'value' => 0]

            ,['od_logistics_status', 'default', 'value' => self::LG_STATUS_INIT]
            ,['od_logistics_status', 'in', 'range' => ConstMap::getConst('od_logistics_status', true)]

            ,['od_pay_mode', 'default', 'value' => self::MODE_FULL_ONLINE_PAY]
            ,['od_pay_mode', 'in', 'range' => ConstMap::getConst('od_pay_mode', true)]

            ,['od_belong_uid', 'required']

            ,['od_operator_uid', 'default', 'value' => 0]

            ,['od_pid', 'default', 'value' => 0]

            ,['od_payed_at', 'default', 'value' => 0]

            ,['od_invalid_at', 'default', 'value' => 0]

            ,['od_number', 'default', 'value' => function(){
                return OrderModel::buildOrderNumber();
            }]
        ];
    }
}
