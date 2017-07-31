<?php
namespace common\models\goods\query;

use yii\base\Object;
use common\models\goods\ar\GoodsRealOption;
use common\helpers\ArrayHelper;

/**
 *
 */
class GoodsOptionQuery extends Object{
    public static function find(){
        return GoodsRealOption::find();
    }



}
