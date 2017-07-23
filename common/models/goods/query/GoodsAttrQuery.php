<?php
namespace common\models\goods\query;

use yii\base\Object;
use common\models\goods\ar\GoodsAttr;

/**
 *
 */
class GoodsAttrQuery extends Object{
    public static function find(){
        return GoodsAttr::find();
    }

}
