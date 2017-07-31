<?php
namespace common\models\goods;

use Yii;
use common\models\Model;
use common\models\goods\GoodsAttrModel;
use common\models\goods\ar\Goods;
use common\models\goods\ar\GoodsAttr;
use common\models\goods\query\GoodsAttrQuery;
use common\helpers\ArrayHelper;

/**
 *
 */
class GoodsModel extends Model
{
    public function createGoods($data){
        Yii::$app->db->beginTransaction();
        if(!$goods = $this->validateCreateGoodsData($data)){
            return false;
        }
        // fix todo
        $goods->g_id = 1;
        // 使用GoodsAttrModel 来创建 商品属性, 同时创建属性选项值
            // 首先选出新的属性和存在的属性
        $gAttrModel = new GoodsAttrModel();
        $gAttrs = $gAttrModel->createGoodsAttrs([
            'attrs' => $data['g_attrs']
        ], $goods);
        if(!$gAttrs){
            list($code, $error) = $gAttrModel->getOneError();
            $this->addError($code, $error);
            return false;
        }
        $skuAttrs = [];
        foreach($gAttrs as $attr){
            if(GoodsAttr::ATR_TYPE_SKU == $attr['g_atr_type']){
                $skuAttrs[] = $attr;
            }
        }
        // 不应该在这个流程中todo
        $skuGoods = $this->createSkuGoods($goods, $skuAttrs);
        if(!$skuGoods){
            return false;
        }

        // 创建sku记录，当然预览sku记录是另外一个方法

        // 创建商品的详细内容记录



        $goods->g_created_at = time();

        return $goods;
    }

    public function createSkuGoods(Goods $goods, $skuAttrs){
        $skuValues = [];
        foreach($skuAttrs as $attr){
            $skuValues[$attr['g_atr_id']] = [];
            foreach($attr['g_atr_opts'] as $opt){
                $skuValues[$attr['g_atr_id']][] = [
                    'value' => sprintf("%s:%s", $attr['g_atr_id'], $opt['g_opt_value']),
                    'name'  => sprintf("%s-%s", $attr['g_atr_show_name'], $opt['g_opt_name'])
                ];
            }
        }
        ksort($skuValues);
        $skuIds = $this->buildSkuIds($skuValues);
        console($skuIds);

    }

    public function buildSkuIds($skuValues){
        $skuIds = [];
        $first = array_shift($skuValues);
        foreach($first as $item){
            foreach($skuValues as $others){
                foreach($others as $otherItem){
                    $skuIds[] = [
                        'value' => implode(';', [$item['value'], $otherItem['value']]),
                        'name' => implode(';', [$item['name'], $otherItem['name']]),
                    ];
                }
                break;
            }
        }
        array_shift($skuValues);
        $next = array_shift($skuValues);
        if(!empty($next)){
            return $this->buildSkuIds(array_merge([$skuIds], [$next]));
        }
        return $skuIds;
    }

    public function validateCreateGoodsData($data){
        $goods = new Goods();
        if(!$goods->load($data, '') || !$goods->validate()){
            $this->addError('', $this->getOneErrMsg($goods));
            return false;
        }


        return $goods;
    }





}
