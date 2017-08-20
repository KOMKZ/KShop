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

    public static function findByValue($values){
        $query = GoodsSku::find()
                         ->where([
                             'g_sku_value' => $values
                         ]);
        return $query;
    }

    public static function findValid(){
        return GoodsSku::find()->where(['in', 'g_sku_status', [GoodsSku::STATUS_ON_SALE, GoodsSku::STATUS_ON_NOT_SALE]]);
    }



    
}
