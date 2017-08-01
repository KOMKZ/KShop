<?php
namespace common\models\goods\ar;

use Yii;
use common\models\staticdata\ConstMap;
use common\models\goods\ar\GoodsClassification;
use common\models\goods\ar\GoodsRealAttr;
use common\models\goods\ar\GoodsAttr;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\goods\GoodsModel;
use yii\helpers\ArrayHelper;
/**
 *
 */
class Goods extends ActiveRecord
{
    CONST STATUS_DRAFT = 'draft';
    CONST STATUS_ON_SALE = 'on_sale';
    CONST STATUS_ON_NOT_SALE = 'on_not_sale';
    CONST STATUS_FORBIDDEN = 'forbidden';
    CONST STATUS_DELETE = 'delete';





    public static function tableName(){
        return "{{%goods}}";
    }

    public function fields(){
        $fields = parent::fields();
        return array_merge($fields, [
            'g_sku_attrs',
            'g_info_attrs',
            'g_option_attrs',
            'g_vaild_sku_ids',
        ]);
    }

    public function getG_vaild_sku_ids(){
        return GoodsModel::createSkuIds($this, ArrayHelper::toArray($this->g_sku_attrs));
    }

    public function getG_sku_attrs(){
        $gaTable = GoodsAttr::tableName();
        $grTable = GoodsRealAttr::tableName();
        return $this
               ->hasMany(GoodsRealAttr::className(), ['g_id' => 'g_id'])
               ->leftJoin("{$gaTable}", "{$gaTable}.g_atr_id = {$grTable}.g_atr_id")
               ->andWhere(['=', "$gaTable.g_atr_type", GoodsAttr::ATR_TYPE_SKU]);
    }

    public function getG_info_attrs(){
        $gaTable = GoodsAttr::tableName();
        $grTable = GoodsRealAttr::tableName();
        return $this
               ->hasMany(GoodsRealAttr::className(), ['g_id' => 'g_id'])
               ->leftJoin("{$gaTable}", "{$gaTable}.g_atr_id = {$grTable}.g_atr_id")
               ->andWhere(['=', "$gaTable.g_atr_type", GoodsAttr::ATR_TYPE_INFO]);
    }

    public function getG_option_attrs(){
        $gaTable = GoodsAttr::tableName();
        $grTable = GoodsRealAttr::tableName();
        return $this
               ->hasMany(GoodsRealAttr::className(), ['g_id' => 'g_id'])
               ->leftJoin("{$gaTable}", "{$gaTable}.g_atr_id = {$grTable}.g_atr_id")
               ->andWhere(['=', "$gaTable.g_atr_type", GoodsAttr::ATR_TYPE_OPTION]);
    }







    public function rules(){
        return [
            ['g_cls_id', 'required'],
            ['g_cls_id', 'exist', 'targetAttribute' => 'g_cls_id', 'targetClass' => GoodsClassification::className()],

            ['g_status', 'required'],
            ['g_status', 'in', 'range' => ConstMap::getConst('g_status', true)],

            ['g_primary_name', 'string'],
            ['g_primary_name', 'required'],

            ['g_secondary_name', 'string'],
            ['g_secondary_name', 'default', 'value' => ''],

            ['g_status', 'in', 'range' => ConstMap::getConst('g_status', true)],
            ['g_status', 'default', 'value' => static::STATUS_DRAFT],

            // todo exist check
            ['g_create_uid', 'required'],

            ['g_updated_at', 'default', 'value' => time()],

            ['g_start_at', 'integer'],

            // todo g_start_at < g_end_at 或者一个时期
            ['g_end_at', 'integer']

        ];
    }




}
