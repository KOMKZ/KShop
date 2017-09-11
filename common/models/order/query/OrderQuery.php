<?php
namespace common\models\order\query;

use yii\base\Object;
use common\models\order\ar\Order;

/**
 *
 */
class OrderQuery extends Object
{
    public static function find(){
        return Order::find();
    }
    
}
