<?php
namespace common\models\goods\query;

use yii\base\Object;
use common\models\goods\ar\Goods;
use common\models\goods\ar\GoodsMeta;
use common\models\goods\ar\GoodsAttr;
use common\models\goods\ar\GoodsRealAttr;

/**
 *
 */
class GoodsQuery extends Object
{
    public static function find(){
        return Goods::find();
    }

    public static function findAttrs(){
        $gaTable = GoodsAttr::tableName();
		$grTable = GoodsRealAttr::tableName();
		return GoodsRealAttr::find()
			   ->leftJoin("{$gaTable}", "{$gaTable}.g_atr_id = {$grTable}.g_atr_id")
			   ->andWhere(['=', "{$grTable}.gr_status", GoodsRealAttr::STATUS_VALID]);
    }
    public static function findMetas(){
        $gaTable = GoodsAttr::tableName();
        $gmTable = GoodsMeta::tableName();
        return GoodsMeta::find()
               ->leftJoin("{$gaTable}", "{$gaTable}.g_atr_id = {$gmTable}.g_atr_id")
               ->select([
                   "{$gmTable}.gm_value",
                   "{$gmTable}.g_atr_id",
                   "{$gaTable}.g_atr_name",
                   "{$gmTable}.gm_id", // 必须有
               ])
               ->andWhere(['=', "$gaTable.g_atr_type", GoodsAttr::ATR_TYPE_META])
               ->andWhere(['=', "{$gmTable}.gm_status", GoodsMeta::STATUS_VALID]);
    }
}
