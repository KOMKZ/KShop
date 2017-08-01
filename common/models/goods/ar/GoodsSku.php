<?php
namespace common\models\goods\ar;

use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
/**
 *
 */
class GoodsSku extends ActiveRecord
{
    const STATUS_ON_SALE = 'sale';
    const STATUS_ON_NOT_SALE = 'on_not_sale';


    public static function tableName(){
        return "{{%goods_sku}}";
    }


    public function rules(){
        return [
            ['g_id', 'required'],
            ['g_id', 'integer'],

            ['g_sku_value', 'required'],
            ['g_sku_value', 'string'],

            ['g_sku_price', 'required'],
            ['g_sku_price', 'integer'],

            ['g_sku_sale_price', 'integer'],
            ['g_sku_sale_price', 'default', 'value' => function(){return $this->g_sku_price;}],

            ['g_sku_status', 'string'],
            ['g_sku_status', 'in', 'range' => ConstMap::getConst('g_sku_status', true)],
            ['g_sku_status', 'default', 'value' => static::STATUS_ON_NOT_SALE],

            ['g_sku_create_uid', 'required'],
            ['g_sku_create_uid', 'integer'],

            ['g_sku_update_uid', 'integer'],

            // todo > 0
            ['g_sku_stock_num', 'required'],
            ['g_sku_stock_num', 'integer'],



        ];
    }
 }
