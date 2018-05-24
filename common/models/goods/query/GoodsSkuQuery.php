<?php
namespace common\models\goods\query;

use yii\base\Object;
use common\models\goods\ar\GoodsSku;
use common\models\goods\ar\Goods;

/**
 *
 */
class GoodsSkuQuery extends Object
{

    public static function find(){
        return GoodsSku::find();
    }

    public static function findByValue($values){
        $query = self::find()
                         ->andWhere([
                             'in', 'g_sku_value', $values
                         ]);
        return $query;
    }

    public static function findValid(){
        return GoodsSku::find()->where(['in', 'g_sku_status', [GoodsSku::STATUS_ON_SALE]]);
    }

    public static function findByWithM(){
        $gTable = Goods::tableName();
        $gskuTable = GoodsSku::tableName();
        return self::find()->leftJoin($gTable, "{$gskuTable}.g_id = {$gTable}.g_id");
    }




}
