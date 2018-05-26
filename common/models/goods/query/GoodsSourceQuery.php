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

    public static function findByWithM(){
        $gTable = Goods::tableName();
        $gsTable = GoodsSource::tableName();
        return self::find()->leftJoin($gTable, "{$gsTable}.g_id = {$gTable}.g_id");
    }



}
