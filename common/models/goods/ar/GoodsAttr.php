<?php
namespace common\models\goods\ar;

use Yii;
use common\models\staticdata\ConstMap;
use common\models\goods\ar\GoodsClassification;
use yii\db\ActiveRecord;
/**
 *
 */
class GoodsAttr extends ActiveRecord
{
    const ATR_TYPE_SKU = 'sku';
    const ATR_TYPE_INFO = 'info';

    const ATR_CLS_TYPE_CLS = 'cls';
    const ATR_CLS_TYPE_GOODS = 'goods';

    public static function tableName(){
        return "{{%goods_attr}}";
    }






    public function rules(){
        return [
            ['g_atr_code', 'string'],
            ['g_atr_code', 'required'],

            ['g_atr_name', 'string'],
            ['g_atr_name', 'required'],

            ['g_atr_show_name', 'string'],
            ['g_atr_show_name', 'default', 'value' => function(){return $this->g_atr_name;}],

            ['g_atr_opt_img', 'default', 'value' => 0],

            ['g_atr_type', 'string'],
            ['g_atr_type', 'in', 'range' => ConstMap::getConst('g_atr_type', true)],
            ['g_atr_type', 'default', 'value' => static::ATR_TYPE_INFO],

            ['g_atr_cls_type', 'required'],
            ['g_atr_cls_type', 'string'],
            ['g_atr_cls_type', 'in', 'range' => ConstMap::getConst('g_atr_cls_type', true)],

            ['g_atr_cls_id', 'required'],
            ['g_atr_cls_id', 'integer'],
            // todo fix because of cls_type cls, goods
            // ['g_atr_cls_id', 'exist', 'targetAttribute' => 'g_cls_id', 'targetClass' => GoodsClassification::className()],

        ];
    }




}
