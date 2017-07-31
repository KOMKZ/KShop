<?php
namespace common\models\goods;

use Yii;
use common\models\Model;
use common\models\goods\ar\GoodsClassification;
use common\models\goods\query\GoodsClassificationQuery;
use common\models\goods\ar\GoodsAttr;
use common\models\goods\ar\GoodsRealAttr;
use common\models\goods\ar\Goods;
use common\models\goods\ar\GoodsRealOption;
use common\models\goods\query\GoodsAttrQuery;
use common\helpers\ArrayHelper;

/**
 *
 */
class GoodsAttrModel extends Model
{

    public function createGoodsAttrs($data, Goods $goods){
        list($newAttrs, $oldAttrs) = $this->parseAttrsNewAndOld($data['attrs']);
        $attrDefs = [];
        
        foreach($oldAttrs as $oldAttr){
            $attrDef = new GoodsRealAttr();
            $attrData = array_merge([
                'g_id' => $goods->g_id,
            ], $oldAttr);
            if(!$attrDef->load($attrData, '') || !$attrDef->validate()){
                $this->addError('', $this->getOneErrMsg($attrDef));
                return false;
            }
            $attrDef->gr_created_at = time();
            if(!$attrDef->insert(false)){
                $this->addError('', Yii::t('商品属性创建失败'));
                return false;
            }
            $options = $this->createAttrOptions(ArrayHelper::getValue($oldAttr, 'g_atr_opts'), $attrDef->g_attr, $goods);
            if(!$options){
                return false;
            }
            $attrDefs[] = $attrDef->toArray();
        }
        console($attrDefs);
    }
    protected function createAttrOptions($options, GoodsAttr $attr, Goods $goods){
        if(is_string($options)){
            $options = [[
                'g_opt_name' => $options,
            ]];
        }
        foreach($options as $key => $optionData){
            $optionData['g_id'] = $goods->g_id;
            $optionData['g_atr_id'] = $attr->g_atr_id;
            $option = $this->createAttrOption($optionData);
            if(!$option){
                return false;
            }
            $options[$key] = $option;
        }
        return $options;
    }
    protected function createAttrOption($optionData){
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
    public function createAttrs($data){
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
