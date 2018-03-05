<?php
namespace common\models\goods\query;

use yii\base\Object;
use common\models\goods\ar\Goods;
use common\models\goods\ar\GoodsSource;

/**
 *
 */
class GoodsSourceQuery extends Object
{
    public static function find(){
        return GoodsSource::find();
    }

    

}
