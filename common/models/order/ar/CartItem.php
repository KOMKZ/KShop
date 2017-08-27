<?php
namespace common\models\order\ar;

use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
use yii\behaviors\TimestampBehavior;


/**
 *
 */
class CartItem extends ActiveRecord
{
    CONST ITEM_STATUS_VALID = 'valid';
    CONST ITEM_STATUS_INVALID = 'invalid';

    CONST OB_TYPE_GOODSSKU = 'sku_goods';

    /**
     * 没有分类
     * @var [type]
     */
    CONST NO_CLASS = 'no_class';

    public static function tableName(){
        return "{{%cart_item}}";
    }
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'ct_created_at',
                'updatedAtAttribute' => null
            ]
        ];
    }
    public function rules(){
        return [
            ['ct_belong_uid', 'required']
            ,['ct_belong_uid', 'integer']

            ,['ct_object_id', 'required']
            ,['ct_object_id', 'integer']

            ,['ct_object_status', 'required']
            ,['ct_object_status', 'in', 'range' => ConstMap::getConst('ct_object_status', true)]

            ,['ct_object_title', 'required']
            ,['ct_object_title', 'string']

            ,['ct_object_data', 'default', 'value' => '']

            ,['ct_object_value', 'default', 'value' => '']

            ,['ct_object_classification', 'default', 'value' => '']

            ,['ct_object_type', 'required']
            ,['ct_object_type', 'string']

            ,['ct_amount', 'required']
            ,['ct_amount', 'default', 'value' => 0]

            ,['ct_price', 'required']
            ,['ct_price', 'integer', 'min' => 0]

            ,['ct_price_type', 'required']
            ,['ct_price_type', 'in', 'range' => ConstMap::getConst('currency_type', true)]
        ];
    }
}
