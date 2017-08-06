<?php
namespace common\models\goods\query;

use yii\base\Object;
use common\models\goods\ar\GoodsSku;

/**
 *
 */
class GoodsSkuQuery extends Object
{

    public static function find(){
        return GoodsSku::find();
    }

}
