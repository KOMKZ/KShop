<?php
namespace common\models\goods\query;

use yii\base\Object;
use common\models\goods\ar\Goods;

/**
 *
 */
class GoodsQuery extends Object
{
    public static function find(){
        return Goods::find();
    }
}
