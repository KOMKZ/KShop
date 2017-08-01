<?php
namespace common\models\goods;

use Yii;
use common\models\Model;
use common\models\goods\GoodsAttrModel;
use common\models\goods\ar\Goods;
use common\models\goods\ar\GoodsAttr;
use common\models\goods\ar\GoodsDetail;
use common\models\goods\query\GoodsAttrQuery;
use common\helpers\ArrayHelper;
use common\models\goods\ar\GoodsSku;

/**
 *
 */
class GoodsModel extends Model
{

    protected function createGoodsBase($data){
        // 首先创建基础属性
        if(!$goods = $this->validateGoodsBaseData($data)){
            return false;
        }
        if(!$goods->insert(false)){
            $this->addError("", Yii::t('', '创建商品基础信息失败'));
            return false;
        }
        return $goods;
    }

    protected function createGoodsDetail($data, Goods $goods){
        // 创建详细说明
        $data['g_id'] = $goods->g_id;
        if(!$goodsDetail = $this->validateGoodsDetailData($data)){
            return false;
        }
        if(!$goodsDetail->insert(false)){
            $this->addError("", Yii::t('', '创建商品详细信息失败'));
            return false;
        }
        return $goodsDetail;
    }

    protected function createGoodsAttrs($data, Goods $goods){
        // 创建商品属性及选项值
        $gAttrModel = new GoodsAttrModel();
        $gAttrs = $gAttrModel->createGoodsAttrs([
            'attrs' => $data['g_attrs']
        ], $goods);
        if(!$gAttrs){
            list($code, $error) = $gAttrModel->getOneError();
            $this->addError($code, "创建商品属性失败:" . $error);
            return false;
        }
        return $gAttrs;
    }

    public function createMultiGoodsSku($skuData, Goods $goods, $asArray = true){
        // todo transaction
        $skuData = ArrayHelper::index($skuData, 'g_sku_value');
        $skuIds = array_keys($skuData);
        $validSkuIds = array_keys($goods->g_vaild_sku_ids);
        $notExistIds = array_diff($skuIds, $validSkuIds);
        if(!empty($notExistIds)){
            $this->addError('', Yii::t('app', "sku值不存在:" . implode(',', $notExistIds)));
            return false;
        }
        $skus = [];
        foreach($skuData as $skuItem){
            $sku = $this->createGoodsSku($skuItem, $goods);
            if(!$sku){
                return false;
            }
            $skus[] = $asArray ? $sku->toArray() : $sku;
        }
        return $skus;
    }

    public function createGoodsSku($skuData, Goods $goods){
        $sku = new GoodsSku();
        if(!$sku->load($skuData, '') || !$sku->validate()){
            $this->addError('', $this->getOneErrMsg($sku));
            return false;
        }
        if(!array_key_exists($skuData['g_sku_value'], $goods->g_vaild_sku_ids)){
            $this->addError('', Yii::t('app', '无效的g_sku_value值:' . $skuData['g_sku_value']));
            return false;
        }
        $sku->g_sku_id = static::buildGSkuId($goods->g_id, $sku->g_sku_value);
        $sku->g_sku_value_name = $goods->g_vaild_sku_ids[$sku->g_sku_value]['name'];
        $sku->g_sku_created_at = time();
        if(!$sku->insert(false)){
            $this->addError("", Yii::t('', '创建商品sku失败'));
            return false;
        }
        return $sku;
    }
    public static function buildGSkuId($gid, $skuValue){
        return $gid . preg_replace('/[;:]/', '', $skuValue);
    }





    public function createGoods($data){
        $t = Yii::$app->db->beginTransaction();
        try {
            if(!$goods = $this->createGoodsBase($data)){
                return false;
            }
            if(!$goodsDetail = $this->createGoodsDetail($data, $goods)){
                return false;
            }
            if(!$gAttrs = $this->createGoodsAttrs($data, $goods)){
                return false;
            }
            $t->commit();
            return $goods;
        } catch (\Exception $e) {
            throw $e;
            Yii::error($e);
            $t->rollback();
            $this->addError('', Yii::t('app', "创建商品发生异常"));
            return false;
        }
    }

    public function deleteGoods(Goods $goods){
        $goods->g_status = Goods::STATUS_DELETE;
        return $goods->update(false);
    }

    public static function createSkuIds(Goods $goods, $skuAttrs){
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
        $skuIds = static::buildSkuIds($skuValues);
        return ArrayHelper::index($skuIds, 'value');
    }

    protected static function buildSkuIds($skuValues){
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
            return static::buildSkuIds(array_merge([$skuIds], [$next]));
        }
        return $skuIds;
    }
    public function validateGoodsDetailData($data){
        $goodsDetail = new GoodsDetail();
        if(!$goodsDetail->load($data, '') || !$goodsDetail->validate()){
            $this->addError('', $this->getOneErrMsg($goodsDetail));
            return false;
        }
        return $goodsDetail;
    }
    public function validateGoodsBaseData($data){
        $goods = new Goods();
        if(!$goods->load($data, '') || !$goods->validate()){
            $this->addError('', $this->getOneErrMsg($goods));
            return false;
        }
        $goods->g_created_at = time();
        return $goods;
    }





}
