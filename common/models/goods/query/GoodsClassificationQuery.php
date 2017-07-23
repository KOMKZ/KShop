<?php
namespace common\models\goods\query;

use yii\base\Object;
use common\models\goods\ar\GoodsClassification;

/**
 *
 */
class GoodsClassificationQuery extends Object{
    public static function find(){
        return GoodsClassification::find();
    }
    public static function findParentsById($clsId){
        $parents = [];
        $one = GoodsClassification::find()
                                  ->where([ 'g_cls_id' => $clsId])
                                  ->one();
        if(!$one){
            return null;
        }elseif(0 == $one->g_cls_pid){
            return [$one];
        }else{
            $r = self::findParentsById($one->g_cls_pid);
            if(null !== $r){
                return array_merge($r, [$one]);
            }
        }
    }
}
