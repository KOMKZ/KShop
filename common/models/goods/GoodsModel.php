<?php
namespace common\models\goods;

use Yii;
use common\models\Model;
use common\models\goods\GoodsAttrModel;
use common\models\goods\ar\Goods;
use common\models\goods\ar\GoodsAttr;
use common\models\goods\ar\GoodsDetail;
use common\models\goods\query\GoodsAttrQuery;
use common\models\goods\query\GoodsSkuQuery;
use common\helpers\ArrayHelper;
use common\models\goods\ar\GoodsSku;
use common\models\staticdata\Errno;

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
            $this->addError(Errno::DB_FAIL_INSERT, Yii::t('app', '创建商品基础信息失败'));
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
            $this->addError(Errno::DB_FAIL_INSERT, Yii::t('app', '创建商品详细信息失败'));
            return false;
        }
        $goods->g_detail = $goodsDetail;
        return $goodsDetail;
    }
    protected function createGoodsMetas($data, Goods $goods, $asArray = true){
        $gAttrModel = new GoodsAttrModel();
        $gMetas = $gAttrModel->createGoodsMetas([
            'metas' => $data
        ], $goods, $asArray);
        if(!$gMetas){
            list($code, $error) = $gAttrModel->getOneError();
            $this->addError($code, "创建商品元属性失败:" . $error);
            return false;
        }
        return $gMetas;
    }

    protected function createGoodsAttrs($data, Goods $goods){
        // 创建商品属性及选项值
        $gAttrModel = new GoodsAttrModel();
        $gAttrs = $gAttrModel->createGoodsAttrs([
            'attrs' => $data
        ], $goods);
        if(!$gAttrs){
            list($code, $error) = $gAttrModel->getOneError();
            $this->addError($code, "创建商品属性失败:" . $error);
            return false;
        }
        return $gAttrs;
    }

    public function createMultiGoodsSku($skuData, Goods $goods, $asArray = true){
        $t = Yii::$app->db->beginTransaction();
        try {
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
            $t->commit();
            return $skus;
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError(Errno::EXCEPTION, Yii::t('app', '创建商品sku异常'));
            return false;
        }
    }

    public function updateMultiGoodsSku($skusData, Goods $goods){
        $t = Yii::$app->db->beginTransaction();
        try {
            // 分析出新的还有旧的
            $oldSkus = $newSkus = [];
            $validSkus = $goods->g_vaild_sku_ids;
            $currentSkus = ArrayHelper::index($goods->g_skus, 'g_sku_value');
            foreach($skusData as $skuData){
                if(empty($skuData['g_sku_value']) || empty($validSkus[$skuData['g_sku_value']]))continue;
                if(array_key_exists($skuData['g_sku_value'], $currentSkus)){
                    $oldSkus[] = $skuData;
                }else{
                    $newSkus[] = $skuData;
                }
            }
            if($newSkus && !$this->createMultiGoodsSku($newSkus, $goods)){
                return false;
            }
            if(!empty($oldSkus)){
                foreach($oldSkus as $skuData){
                    if(false === $this->updateGoodsSku($currentSkus[$skuData['g_sku_value']], $skuData, $goods)){
                        return false;
                    }
                }
            }
            $goods->refresh();
            $t->commit();
            //maybe change
            return true;
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError(Errno::EXCEPTION, Yii::t("app", "更新产品sku异常"));
            return false;
        }
    }

    public function updateGoodsSku(GoodsSku $sku, $skuData, Goods $goods){
        if(empty($sku->g_sku_id)){
            return false;
        }
        if(!$sku->load($skuData, '') || !$sku->validate()){
            return false;
        }
        if(false === $sku->update(false)){
            $this->addError(Errno::DB_FAIL_UPDATE, Yii::t('app', "更新产品sku失败"));
            return false;
        }
        return $sku;
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
        // $sku->g_sku_id = static::buildGSkuId($goods->g_id, $sku->g_sku_value);
        $sku->g_sku_value_name = $goods->g_vaild_sku_ids[$sku->g_sku_value]['name'];
        $sku->g_sku_created_at = time();
        if(!$sku->insert(false)){
            $this->addError(Errno::DB_FAIL_INSERT, Yii::t('app', '创建商品sku失败'));
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
            if(!$gMetas = $this->createGoodsMetas($data['g_metas'], $goods)){
                return false;
            }
            if(!$gAttrs = $this->createGoodsAttrs($data['g_attrs'], $goods)){
                return false;
            }
            $t->commit();
            return $goods;
        } catch (\Exception $e) {
            Yii::error($e);
            $t->rollback();
            $this->addError(Errno::EXCEPTION, Yii::t('app', "创建商品发生异常"));
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

    public function updateGoods($data, Goods $goods){
        if(empty($goods->g_id)){
            $this->addError('', Yii::t('app', "商品g_id不存在"));
            return false;
        }

        if(!$goods = $this->updateGoodsBase($data['base'], $goods)){
            return false;
        }

        $detailObject = $goods->g_detail;
        if($detailObject && (!$goodsDetail = $this->updateGoodsDetail($data['detail'], $goods))){
            return false;
        }
        if(!$detailObject && (!$goodsDetail = $this->createGoodsDetail($data['detail'], $goods))){
            return false;
        }

        // update, create, delete meta of goods
        $delRows = GoodsAttrModel::deleteGoodsMetas(['in', 'gm_id', $data['g_del_meta_ids']]);
        if(false === $delRows){
            $this->addError(Errno::DB_FAIL_MDELETE, Yii::t('app', '删除多条商品元属性出错'));
            return false;
        }
        $oldMetaData = $newMetaData = [];
        foreach($data['meta']['g_metas'] as $metaData){
            if(!array_key_exists('gm_id', $metaData)){
                $newMetaData[] = $metaData;
            }elseif(array_key_exists('gm_id', $metaData) && !in_array($metaData['gm_id'], $data['g_del_meta_ids'])){ //
                $oldMetaData[] = $metaData;
            }
        }

        if($oldMetaData && !$this->updateGoodsMetas($oldMetaData, $goods)){
            return false;
        }
        if($newMetaData && !$this->createGoodsMetas($newMetaData, $goods, false)){
            return false;
        }
        unset($newMetaData, $oldMetaData);

        // 更新商品sku属性和选项属性
        // 首先进行进行删除操作
        if(!empty($data['g_del_atr_ids'])){
            $delRows = GoodsAttrModel::deleteGoodsAttrs(['in', 'gr_id', $data['g_del_atr_ids']]);
            if(false === $delRows){
                $this->addError(Errno::DB_FAIL_MDELETE, Yii::t('app', '删除多条商品属性出错'));
                return false;
            }
        }
        // 分别新的属性和旧的属性设置
        $oldAttrData = $newAttrData = [];
        foreach($data['attrs']['g_attrs'] as $attrData){
            if(!array_key_exists('gr_id', $attrData)){
                $newAttrData[] = $attrData;
            }elseif(array_key_exists('gr_id', $attrData) && !in_array($attrData['gr_id'], $data['g_del_atr_ids'])){ //
                $oldAttrData[] = $attrData;
            }
        }
        if($newAttrData && !$this->createGoodsAttrs($newAttrData, $goods)){
            return false;
        }
        if($oldAttrData && !$this->updateGoodsAttrs($oldAttrData, $goods)){
            return false;
        }

        // 确保sku实例此时是争取的
        self::ensureSkuValid($goods);



        console($goods->toArray());
    }

    public static function ensureSkuValid($goods){
        $validSkuMap = static::createSkuIds($goods, ArrayHelper::toArray($goods->g_sku_attrs));
        return GoodsSku::updateAll(['g_sku_status' => GoodsSku::STATUS_INVALID], [
            'and',
            ['=', 'g_id', $goods->g_id],
            ['not in', 'g_sku_value', array_keys($validSkuMap)]
        ]);
    }

    protected function updateGoodsAttrs($attrData, Goods $goods, $asArray = true){
        $t = Yii::$app->db->beginTransaction();
        try {
            $attrs = ArrayHelper::index($goods->g_attrs, 'gr_id');
            foreach($attrData as $attrItem){
                if(!isset($attrItem['gr_id']) || !array_key_exists($attrItem['gr_id'], $attrs))continue;
                if(!$attr = $this->updateGoodsAttr($attrs[$attrItem['gr_id']], $attrItem, $goods))return false;
            }
            $t->commit();
            return $attrs;
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError(Errno::EXCEPTION, Yii::t('app', "修该商品多项属性失败"));
            return false;
        }
    }

    protected function updateGoodsAttr($realAttr, $attrData, Goods $goods){
        if(!empty($realAttr)){
            // 商品属性约定不能修改，只能修改商品属性值
            if(empty($attrData['g_atr_opts'])){
                return $realAttr;
            }
            // 解析得到新的属性还有旧的属性
            $oldOpts = $newOpts = [];
            foreach ($attrData['g_atr_opts'] as $optionData) {
                if(!empty($optionData['g_opt_id'])){
                    $oldOpts[] = $optionData;
                }else{
                    $newOpts[] = $optionData;
                }
            }
            $attrModel = new GoodsAttrModel();
            if($oldOpts && !$attrModel->updateAttrOptions($oldOpts, $realAttr, $goods, false)){
                list($code, $error) = $attrModel->getOneError();
                $this->addError('', $error);
                return false;
            }
            if($newOpts && !$newOptions = $attrModel->createAttrOptions($newOpts, $realAttr->g_attr, $goods, false, $realAttr->next_opt_value)){
                list($code, $error) = $attrModel->getOneError();
                $this->addError('', $error);
                return false;
            }
            $realAttr->refresh();
        }
        return $realAttr;
    }

    protected function updateGoodsMetas($metasData, Goods $goods, $asArray = true){
        $t = Yii::$app->db->beginTransaction();
        try {
            $metas = ArrayHelper::index($goods->g_metas, 'gm_id');
            foreach($metasData as $metaData){
                if(!isset($metaData['gm_id']) || !array_key_exists($metaData['gm_id'], $metas))continue;
                if(!$meta = $this->updateGoodsMeta($metas[$metaData['gm_id']], $metaData, $goods))return false;
                $metas[$metaData['gm_id']] = $meta;
            }
            $t->commit();
            return $metas;
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError(Errno::EXCEPTION, Yii::t('app', "修该商品多项元属性失败"));
            return false;
        }
    }

    protected function updateGoodsMeta($meta, $metaData, Goods $goods){
        if(!empty($metaData)){
            if(!$meta->load($metaData, '') || !$meta->validate()){
                $this->addError('', $this->getOneErrMsg($meta));
                return false;
            }
            if(false === $meta->update(false)){
                $this->addError(Errno::DB_FAIL_UPDATE, Yii::t('app', "更新商品元数据失败"));
                return false;
            }
        }
        return $meta;
    }

    protected function updateGoodsDetail($detailData, Goods $goods){
        if(!empty($detailData)){
            $detailObj = $goods->g_detail;
            if(!$detailObj->load($detailData, '') || !$detailObj->validate()){
                $this->addError('', $this->getOneErrMsg($detailObj));
                return false;
            }
            if(false === $detailObj->update(false)){
                $this->addError(Errno::DB_FAIL_UPDATE, Yii::t('app', "更新商品详细数据失败"));
                return false;
            }
        }
        $goods->g_detail = $detailObj;
        return $detailObj;
    }
    protected function updateGoodsBase($baseData, Goods $goods){
        if(!empty($baseData)){
            $goods->scenario = 'update';
            if(!$goods->load($baseData, '') || !$goods->validate()){
                $this->addError('', $this->getOneErrMsg($goods));
                return false;
            }
            if(false === $goods->update(false)){
                $this->addError(Errno::DB_FAIL_UPDATE, Yii::t('app', "更新商品基础数据失败"));
                return false;
            }
        }
        return $goods;
    }





}
