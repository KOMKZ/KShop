<?php
namespace common\models\order\ar;

use Yii;
use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
use common\models\order\OrderModel;
use yii\behaviors\TimestampBehavior;
use common\validators\TypeValidator;
use common\models\goods\ar\GoodsSku;
/**
 *
 */
class Order extends ActiveRecord
{
    const OD_TYPE_GOODS = 'goods';

    const PAY_STATUS_NOPAY = 'nopay';
    const PAY_STATUS_PAYED = 'payed';

    const COMMENT_STATUS_NOCOMMENT = 'nocomment';
    const COMMENT_STATUS_COMMENTED = 'commented';

    const RF_STATUS_NORF = 'norf';

    const STATUS_SUBMIT = 'submit';

    const LG_STATUS_INIT = 'init';

    const MODE_FULL_ONLINE_PAY = 'fullpay';

    public $goods_sku_data;

    private $_orderGoods = [];

    public function fields(){
        $fields = parent::fields();
        return array_merge($fields, [
            'order_goods'
        ]);
    }

    public function getOrder_goods(){
        return $this->_orderGoods;
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

    public function addOrderGoods(OrderGoods $orderGoods){
        array_push($this->_orderGoods, $orderGoods);
    }

    public function validateGoods_sku_data($attr){
        $skuData = $this->$attr;
        foreach($skuData as $skuObject){
            if($skuObject instanceof GoodsSku)continue;
            $this->addError('', Yii::t('app', "goods_sku_data中每个元素都必须是GoodsSku"));
        }
    }

    public function rules(){
        return [
            ['od_type', 'default', 'value' => self::OD_TYPE_GOODS]
            ,['od_type', 'in', 'range' => ConstMap::getConst('od_type', true)]
            ,['goods_sku_data', 'required']
            ,['goods_sku_data', TypeValidator::className(), 'expectType' => 'array']
            ,['goods_sku_data', 'validateGoods_sku_data']

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
