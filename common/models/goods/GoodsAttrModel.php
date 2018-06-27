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
use common\models\goods\query\GoodsQuery;
use common\helpers\ArrayHelper;
use common\models\staticdata\Errno;


/**
 *
 */
class GoodsAttrModel extends Model
{

    /**
     * 根据筛选条件删除商品元数据
     * 注意该删除是伪删除
     * @param  array $condition 筛选数据的条件
     * @param  array  $params    [description]
     * @return integer            返回影响的函数
     */
    public static function deleteGoodsMetas($condition = null, $params = []){
        return GoodsMeta::updateAll(['gm_status' => GoodsMeta::STATUS_DELETE], $condition, $params);
    }

    /**
     * 根据筛选条件删除商品属性68
     * 注意该删除是伪删除
     * @param  array $condition 筛选数据的条件
     * @param  array  $params    [description]
     * @return integer            返回影响的函数
     */
    public static function deleteGoodsAttrs($condition = null, $params = []){
        return GoodsRealAttr::updateAll(['gr_status' => GoodsRealAttr::STATUS_DELETE], $condition, $params);
    }

    /**
     * 更新商品多个属性
     * @param  array  $attrData 属性数据
     * @save \common\models\goods\ar\GoodsAttr::updateGoodsAttr 哪些数据可以修改
     * @param  Goods   $goods    商品基础数据
     * @param  boolean $asArray  是否返回为数组
     * @return boolean|array     返回false或者多个更新之后的属性数据
     */
    public function updateGoodsAttrs($attrData, Goods $goods, $asArray = true){
        $t = Yii::$app->db->beginTransaction();
        try {
            foreach($attrData as $attrItem){
                $attr = GoodsQuery::findAttrs()
                                ->andWhere(['=', 'gr_id', $attrItem['gr_id']])
                                ->one();
                if(!$attr){
                    $this->addError("", sprintf("指定的属性不存在%s", $attrItem['gr_id']));
                    return false;
                }
                if(!$attr = $this->updateGoodsAttr($attr, $attrItem, $goods))return false;
                $attrs[$attrItem['gr_id']] = $attr;
            }
            $t->commit();
            return $attrs;
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError(Errno::EXCEPTION, Yii::t('app', "修该商品多项属性失败"));
            return false;
        }
    }

    /**
     * 修改单个属性数据
     * 本方法会创建新的属性选项值，同时更新旧的数据选项值
     * @param  \common\models\goods\ar\GoodsRealAttr $realAttr 真实商品属性对象
     * @param  array $attrData 修改的商品属性数据
     * - g_atr_opts: array, 属性选项值数组
     * @see \common\models\goods\GoodsAttrModel::createAttrOptions 了解创建的数据结构
     * @see \common\models\goods\GoodsAttrModel::updateAttrOptions 了解更新时的数据结构
     * @param  Goods  $goods    商品属性数据对象
     * @return \common\models\goods\ar\GoodsRealAttr           返回真实商品属性对象
     */
    public function updateGoodsAttr($realAttr, $attrData, Goods $goods){
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
            if($oldOpts && !$this->updateAttrOptions($oldOpts, $realAttr, $goods, false)){
                list($code, $error) = $this->getOneError();
                $this->addError('', $error);
                return false;
            }
            if($newOpts && !$newOptions = $this->createAttrOptions($newOpts, $realAttr->g_attr, $goods, false, $realAttr->next_opt_value)){
                list($code, $error) = $this->getOneError();
                $this->addError('', $error);
                return false;
            }
            $realAttr->refresh();
        }
        return $realAttr;
    }

    /**
     * 更新多个商品元数据
     * @param  array  $metasData 多个商品元数据
     * @see \common\models\goods\GoodsAttrModel::updateGoodsMeta 了解具体的更新内容
     * @param  Goods   $goods     商品基础信息对象
     * @param  boolean $asArray   返回数据元素是否为数组
     * @return array             false, 成功时返回元数据数据数组
     */
    public function updateGoodsMetas($metasData, Goods $goods, $asArray = true){
        $t = Yii::$app->db->beginTransaction();
        try {

            $metas = ArrayHelper::index($goods->g_metas, 'gm_id');
            foreach($metasData as $metaData){
                $meta = GoodsQuery::findMetas()
                                ->andWhere(['=', 'gm_id', $metaData['gm_id']])
                                ->one();
                if(!$meta){
                    $this->addError("", sprintf("指定的元属性不存在%s", $metaData['gm_id']));
                    return false;
                }
                $metaData['g_id'] = $goods->g_id;
                if(!$meta = $this->updateGoodsMeta($meta, $metaData, $goods))return false;
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

    /**
     * 更新单个商品元数据
     * @param  array $meta     单个商品元数据
     * - gm_id: integer, 旧的元数据id，用于唯一确定
     * - gm_value: string, 旧的元数据的值
     * - gm_status: string, 旧的元数据的状态
     * @param  [type] $metaData [description]
     * @param  Goods  $goods    [description]
     * @return \common\models\goods\ar\GoodsMeta           元数据属性对象
     */
    public function updateGoodsMeta($meta, $metaData, Goods $goods){
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

    /**
     * 创建多个商品元数据
     * @param  array  $data    商品多个元数据
     * 本方法通过gm_id判断出原属性和旧属性
     * 单个元素定义如下：
     * - gm_value: string,
     * - gm_atr_code: string, 元属性代号
     * - gm_atr_name: string, 原书行名称
     * @param  \common\models\goods\GoodsModel  $goods   商品基础信息对象
     * @param  boolean $asArray 返回对象元素是否时数组
     * @return [type]           [description]
     */
    public function createGoodsMetas($data, $goods, $asArray = true){
        list($newMetas, $oldMetas) = $this->parseMetaNewAndOld($data['metas']);
        $metas = [];
        foreach($newMetas as $meta){
            $meta['g_atr_cls_type'] = GoodsAttr::ATR_CLS_TYPE_GOODS;
            $meta['g_atr_cls_id'] = $goods->g_id;
            $attrObj = new GoodsAttr();
            $metaObject = $this->createAttr($meta, $attrObj);
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
                $this->addErrors($metaDef->getErrors());
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

    /**
     * 从提供的数据中区分出新的元属性和旧的元属性
     * @param  [type] $metas 元属性数据
     * @see \common\models\goods\GoodsModel::createGoodsMetas
     * @return array
     * - 0: 新数据
     * - 1: 旧数据
     */
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

    /**
     * 创建多个商品属性
     * @param  array  $data    多个商品属性数据
     * @see \common\models\goods\GoodsAttrModel::createAttr 了解创建的数据结构
     * 额外数据：
     * - g_atr_opts: array 商品属性的选项值 @see \common\models\goods\GoodsAttrModel::createAttrOption
     * @param  Goods   $goods   商品基础信息对象
     * @param  boolean $asArray 返回数据元素是否是数组
     * @return [type]           返回多个创建成功的属性数据
     */
    public function createGoodsAttrs($data, Goods $goods, $asArray = true){
        list($newAttrs, $oldAttrs) = $this->parseAttrsNewAndOld($data['attrs']);
        $attrs = [];
        foreach($newAttrs as $attr){
            $attr['g_atr_cls_type'] = GoodsAttr::ATR_CLS_TYPE_GOODS;
            $attr['g_atr_cls_id'] = $goods->g_id;
            $attrObj = new GoodsAttr();
            $attrObject = $this->createAttr($attr, $attrObj);
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

    /**
     * 创建属性的选项值
     * @param  array    $options    选项值数组
     * @see \common\models\goods\GoodsAttrModel::createAttrOption 了解可以创建的数据结构
     * @param  GoodsAttr $attr       选项值需要关联属性对象
     * @param  Goods     $goods      所属的商品数据对象
     * @param  boolean   $asArray    [description]
     * @param  integer   $startValue 选项值的起始数值
     * 注意默认从1开始，更新时创建新的需要自己查处最大的值
     * @return array                返回选项值数据数组
     */
    public function createAttrOptions($options, GoodsAttr $attr, Goods $goods, $asArray = true, $startValue = 1){
        if(is_string($options)){
            $cands = preg_split("/\s*,\s*/", $options, -1, PREG_SPLIT_NO_EMPTY);
            if(!$cands){
                $this->addError("", "选项值设置错误");
                return false;
            }
            $options = [];
            foreach($cands as $value){
                $options[] = [
                    'g_opt_name' => $value,
                ];
            }
        }
        $optionValue = $startValue;
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

    public function updateAttrOptions($optionsData, GoodsRealAttr $attr, Goods $goods, $asArray = true){
        $options = ArrayHelper::index($attr->g_atr_opts, "g_opt_id");
        foreach($optionsData as $optionData){
            if(!empty($options[$optionData['g_opt_id']]) && !$option = $this->updateAttrOption($options[$optionData['g_opt_id']], $optionData)){
                return false;
            }
        }
        return $options;
    }

    /**
     * [updateAttrOption description]
     * @param  GoodsRealOption $option     [description]
     * @param  array          $optionData 选项值数据
     * - g_opt_id: integer,required 选项值id
     * - g_opt_name: string,required 选项值名称
     * - g_opt_img: string 选项值图片
     * @return [type]                      [description]
     */
    public function updateAttrOption(GoodsRealOption $option, $optionData){
        if(empty($option['g_opt_id'])){
            $this->addError('', Yii::t('app', "更新属性选项对象出错，g_opt_id不存在"));
            return false;
        }
        $option->scenario = 'update';
        if(!$option->load($optionData, '') || !$option->validate()){
            $this->addError('', $this->getOneErrMsg($option));
            return false;
        }
        if(false === $option->update(false)){
            $this->addError(Errno::DB_FAIL_UPDATE, Yii::t('app', "更新商品元数据失败"));
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
    public function createAttr($attrData, GoodsAttr $goodsAttr){
        if(!$goodsAttr->load($attrData, '') || !$goodsAttr->validate()){
            $this->addErrors($goodsAttr->getErrors());
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
