<?php
namespace common\models\goods;

use Yii;
use common\models\Model;
use common\models\goods\ar\Goods;
use common\models\goods\query\GoodsAttrQuery;
use common\helpers\ArrayHelper;

/**
 *
 */
class GoodsModel extends Model
{
    public function createGoods($data){
        if(!$goods = $this->validateCreateGoodsData($data)){
            return false;
        }
        // 使用GoodsAttrModel 来创建 商品属性, 同时创建属性选项值

        // 创建sku记录，当然预览sku记录是另外一个方法

        // 创建商品的详细内容记录



        $goods->g_created_at = time();

        return $goods;
    }

    public function validateCreateGoodsData($data){
        $goods = new Goods();
        if(!$goods->load($data, '') || !$goods->validate()){
            $this->addError('', $this->getOneErrMsg($goods));
            return false;
        }
        if(!is_array($data['g_attrs'])){
            $this->addError('', Yii::t('app', 'g_attrs必须是一个数组'));
            return false;
        }

        return $goods;
    }





}
