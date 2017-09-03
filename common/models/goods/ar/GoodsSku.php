<?php
namespace common\models\goods\ar;

use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
use common\models\goods\ar\GoodsSource;
use common\models\goods\query\GoodsSkuQuery;
use yii\helpers\ArrayHelper;
use common\models\goods\ar\Goods;

/**
 *
 */
class GoodsSku extends ActiveRecord
{
    const STATUS_ON_SALE = 'sale';
    const STATUS_ON_NOT_SALE = 'on_not_sale';
    CONST STATUS_INVALID = 'invalid';

    protected static $currentSkuValues = [];
    public static function tableName(){
        return "{{%goods_sku}}";
    }

    public function fields(){
        $fields = parent::fields();
        return array_merge($fields, [
            'g_sku_source',
            'g_opt_limits'
        ]);
    }

    public function getGoods(){
        return $this->hasOne(Goods::className(), ['g_id' => 'g_id']);
    }

    public function getCart_item_info(){
        // todo json 使用这些保存为当时的信息
        return '';
    }

    public function getCart_item_title(){
        return $this->g_sku_name . ',' . $this->g_sku_value_name;
    }

    public function getG_opt_limits(){
        if(empty(static::$currentSkuValues)){
            static::$currentSkuValues = GoodsSkuQuery::findValid()
                                            ->select(['g_sku_value'])
                                            ->andWhere([
                                                '=', 'g_id', $this->g_id
                                            ])
                                            ->asArray()
                                            ->all();
            if(empty(static::$currentSkuValues)){
                return [];
            }
            static::$currentSkuValues = array_map(function($value){
                preg_match_all('/(?P<atr>[0-9]+):(?P<opt>[0-9]+)/', $value, $matches);
                return array_combine($matches['atr'], $matches['opt']);
            }, ArrayHelper::getColumn(static::$currentSkuValues, 'g_sku_value'));
        }

        preg_match_all('/(?P<atr>[0-9]+):(?P<opt>[0-9]+)/', $this->g_sku_value, $matches);
        $curMap = array_combine($matches['atr'], $matches['opt']);
        $validMap = [];
        foreach($curMap as $curAtrId => $curOptId){
            $candidate = [];
            foreach(static::$currentSkuValues as $key => $item){
                foreach($item as $atrId => $optId){
                    if($curAtrId == $atrId)continue;
                    if($curMap[$atrId] == $optId){
                        $candidate[$atrId][] = $item[$curAtrId];
                    }elseif(!isset($candidate[$atrId])){
                        $candidate[$atrId] = [];
                    }
                }
            }
            $minimum = array_pop($candidate);
            foreach($candidate as $item){
                $minimum = array_uintersect($minimum, $item, function ($v1,$v2){if($v1===$v2){return 0;}if ($v1 > $v2) return 1;return -1;});
            }
            $validMap[$curAtrId] = implode(',', array_unique($minimum));
        }
        return $validMap;

    }

    public function getG_sku_source(){
        return $this->hasMany(GoodsSource::className(), [
            'gs_cls_id' => 'g_sku_id'
        ])->andWhere([
            '=', 'gs_cls_type', GoodsSource::CLS_TYPE_SKU
        ]);
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
