<?php
namespace common\models\goods;

use Yii;
use common\models\Model;
use common\models\goods\ar\GoodsClassification;
use common\models\goods\query\GoodsClassificationQuery;
use common\models\goods\ar\GoodsAttr;
use common\models\goods\ar\GoodsRealAttr;
use common\models\goods\ar\Goods;
use common\models\goods\ar\GoodsMeta;
use common\models\goods\ar\GoodsRealOption;
use common\models\goods\query\GoodsAttrQuery;
use common\helpers\ArrayHelper;


/**
 *
 */
class GoodsAttrModel extends Model
{
    public static function deleteGoodsMetas($condition = null, $params = []){
        return GoodsMeta::updateAll(['gm_status' => GoodsMeta::STATUS_DELETE], $condition, $params);
    }

    public static function deleteGoodsAttrs($condition = null, $params = []){
        return GoodsRealAttr::updateAll(['gr_status' => GoodsRealAttr::STATUS_DELETE], $condition, $params);
    }



    public function createGoodsMetas($data, $goods, $asArray = true){
        list($newMetas, $oldMetas) = $this->parseMetaNewAndOld($data['metas']);
        $metas = [];
        foreach($newMetas as $meta){
            $meta['g_atr_cls_type'] = GoodsAttr::ATR_CLS_TYPE_GOODS;
            $meta['g_atr_cls_id'] = $goods->g_id;
            $metaObject = $this->createAttr($meta);
            if(!$metaObject){
                return false;
            }
            $meta['g_atr_id'] = $metaObject->g_atr_id;
            $metas[] = $meta;
        }

        $metaDefs = [];
        $metas = array_merge($metas, $oldMetas);
        foreach($metas as $meta){
            $metaDef = new GoodsMeta();
            $metaData = array_merge([
                'g_id' => $goods->g_id,
            ], $meta);
            if(!$metaDef->load($metaData, '') || !$metaDef->validate()){
                $this->addError('', $this->getOneErrMsg($metaDef));
                return false;
            }
            $metaDef->gm_created_at = time();
            if(!$metaDef->insert(false)){
                $this->addError('', Yii::t('商品元属性创建失败'));
                return false;
            }
            $metaDefs[] = $asArray ? $metaDef->toArray() : $metaDef;
        }
        return $metaDefs;
    }

    public function parseMetaNewAndOld($metas){
        $newMetas = [];
        $oldMeta = [];
        foreach($metas as $item){
            if(empty($item['g_atr_id'])){
                $newMetas[] = $item;
            }else{
                $oldMeta[] = $item;
            }
        }
        return [$newMetas, $oldMeta];
    }


    public function createGoodsAttrs($data, Goods $goods, $asArray = true){
        list($newAttrs, $oldAttrs) = $this->parseAttrsNewAndOld($data['attrs']);
        $attrs = [];
        foreach($newAttrs as $attr){
            $attr['g_atr_cls_type'] = GoodsAttr::ATR_CLS_TYPE_GOODS;
            $attr['g_atr_cls_id'] = $goods->g_id;
            $attrObject = $this->createAttr($attr);
            if(!$attrObject){
                return false;
            }
            $attr['g_atr_id'] = $attrObject->g_atr_id;
            $attrs[] = $attr;
        }
        $attrDefs = [];
        $attrs = array_merge($attrs, $oldAttrs);
        foreach($attrs as $attr){
            $attrDef = new GoodsRealAttr();
            $attrData = array_merge([
                'g_id' => $goods->g_id,
            ], $attr);
            if(!$attrDef->load($attrData, '') || !$attrDef->validate()){
                $this->addError('', $this->getOneErrMsg($attrDef));
                return false;
            }
            $attrDef->gr_created_at = time();
            if(!$attrDef->insert(false)){
                $this->addError('', Yii::t('商品属性创建失败'));
                return false;
            }
            $options = $this->createAttrOptions(ArrayHelper::getValue($attr, 'g_atr_opts'), $attrDef->g_attr, $goods);
            if(!$options){
                return false;
            }
            $attrDefs[] = $asArray ? $attrDef->toArray() : $attrDef;
        }
        return $attrDefs;
    }
    protected function createAttrOptions($options, GoodsAttr $attr, Goods $goods){
        if(is_string($options)){
            $options = [[
                'g_opt_name' => $options,
            ]];
        }
        $optionValue = 1;
        foreach($options as $key => $optionData){
            $optionData['g_id'] = $goods->g_id;
            $optionData['g_atr_id'] = $attr->g_atr_id;
            $optionData['g_opt_value'] = $optionValue;
            $option = $this->createAttrOption($optionData);
            if(!$option){
                return false;
            }
            $optionValue++;
            $options[$key] = $option;
        }
        return $options;
    }

    /**
     * 创建一个商品属性选项值
     * 选项值需要外部逻辑指定并保持唯一
     * @param  [type] $optionData 选项数据
     * @return [type]             [description]
     */
    public function createAttrOption($optionData){
        $option = new GoodsRealOption();
        if(!$option->load($optionData, '') || !$option->validate()){
            $this->addError('', $this->getOneErrMsg($option));
            return false;
        }
        $option->g_opt_created_at = time();
         if(!$option->insert(false)){
            $this->addError('', Yii::t('商品属性值创建失败'));
            return false;
        }
        return $option;
    }

    /**
     * 分析出新的属性和旧的属性
     * $attrs 中的每个元素都是数组，如果该元素含有g_atr_id则为旧属性，否则为旧属性
     * @param  [type] $attrs [description]
     * @return [type]        [description]
     */
    protected function parseAttrsNewAndOld($attrs){
        $newAttrs = [];
        $oldAttrs = [];
        foreach($attrs as $item){
            if(empty($item['g_atr_id'])){
                $newAttrs[] = $item;
            }else{
                $oldAttrs[] = $item;
            }
        }
        return [$newAttrs, $oldAttrs];
    }

    /**
     * 创建一个分类属性
     * @param  array $attrData 属性数据
     * - g_atr_name: string,required 属性名称
     * - g_atr_code: string,required 属性标识
     * - g_atr_show_name: string,optional 属性展示名称，空时则默认值时g_atr_name
     * - g_atr_opt_img: integer,optional,default:0 属性值是否支持图片 @see ConstMap::getConst('g_atr_opt_img')
     * - g_atr_type: string, optional,default:info 属性的类型 @see ConstMap::getConst('g_atr_opt_img')
     * - g_atr_cls_type: string, required 属性所属分类的类型 @see ConstMap::getConst('g_atr_cls_type')
     * - g_atr_cls_id: integer, required 属性所属分类id
     * @return object 返回属性数据对象
     */
    public function createAttr($attrData){
        $goodsAttr = new GoodsAttr();
        if(!$goodsAttr->load($attrData, '') || !$goodsAttr->validate()){
            $this->addError('', $this->getOneErrMsg($goodsAttr));
            return false;
        }
        $goodsAttr->g_atr_created_at = time();
        if(!$goodsAttr->insert(false)){
            $this->addError('', Yii::t('app', '创建属性失败'));
            return false;
        }
        return $goodsAttr;
    }

    /**
     * 添加属性列表到对应的分类中
     * 一个属性只能添加到一个分类中，一个属性的添加失败将导致所有添加失败
     * @param array $data  属性列表
     * 其中每个元素的定义如下：
     * - g_atr_code: string, requird, 属性码
     * - g_atr_name: string,requird, 属性名称
     * - g_atr_show_name: string, 属性展示名陈，空时自动填充为属性名称
     * - g_atr_cls_id: integer, required,分类名称
     * @param integer 返回添加成功的属性数量
     */
    public function batchCreateAttrs($data){
        foreach($data as $key => $dataItem){
            $goodsAttr = new GoodsAttr();
            if(!$goodsAttr->load($dataItem, '') || !$goodsAttr->validate()){
                $this->addError('', $this->getOneErrMsg($goodsAttr));
                return false;
            }
            $goodsAttr->g_atr_created_at = time();
            $data[$key] = $goodsAttr->toArray();
            ksort($data[$key]);
        }
        $columns = [
            'g_atr_code',
            'g_atr_cls_type',
            'g_atr_name',
            'g_atr_show_name',
            'g_atr_cls_id',
            'g_atr_opt_img',
            'g_atr_created_at',
            'g_atr_type'
        ];
        sort($columns);
        return Yii::$app->db->createCommand()
                            ->batchInsert(GoodsAttr::tableName(), $columns, $data)
                            ->execute();
    }
}
