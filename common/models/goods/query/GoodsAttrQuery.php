<?php
namespace common\models\goods\query;

use yii\base\Object;
use common\models\goods\ar\GoodsAttr;
use common\models\goods\query\GoodsClassificationQuery;
use common\helpers\ArrayHelper;

/**
 *
 */
class GoodsAttrQuery extends Object{
    public static function find(){
        return GoodsAttr::find();
    }
    /**
     * 通过分类查询得到属性对象
     * @param  [type]  $clsId       [description]
     * @param  boolean $returnQuery [description]
     * @return [type]               [description]
     */
    public static function findAttrsByClsid($clsId){
        $clsObjects = GoodsClassificationQuery::findParentsById($clsId);
        $clsIds = array_keys(ArrayHelper::index($clsObjects, 'g_cls_id'));
        $query = GoodsAttr::find()
                        ->where([
                            'g_atr_cls_id' => $clsIds
                        ]);
        return $query;
    }

}
