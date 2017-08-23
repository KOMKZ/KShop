<?php
namespace common\models\pay\query;

use yii\base\Object;
use common\models\pay\ar\PayTrace;
/**
 *
 */
class PayTraceQuery extends Object
{
    public static function find(){
        return PayTrace::find();
    }
}
