<?php
namespace common\models\trans\query;

use yii\base\Object;
use common\models\trans\ar\Transaction;

/**
 *
 */
class TransactionQuery extends Object
{
    public static function find(){
        return Transaction::find();
    }

    public static function findConsume(){
        return self::find()->where(['t_type' => Transaction::TYPE_CONSUME]);
    }
}
