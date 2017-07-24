<?php
namespace common\models\goods;

use Yii;
use common\models\Model;
use common\models\goods\ar\GoodsClassification;
use common\models\goods\query\GoodsClassificationQuery;
use common\models\goods\ar\GoodsAttr;
use common\models\goods\query\GoodsAttrQuery;
/**
 *
 */
class GoodsAttrModel extends Model
{
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
            'g_atr_name',
            'g_atr_show_name',
            'g_atr_cls_id',
            'g_atr_created_at'
        ];
        sort($columns);
        return Yii::$app->db->createCommand()
                            ->batchInsert(GoodsAttr::tableName(), $columns, $data)
                            ->execute();
    }
}
