<?php
namespace common\models\message\query;

use yii\base\Object;
use common\models\message\ar\MessageTpl;

/**
 *
 */
class MessageTplQuery extends Object
{
    public static function find(){
        return MessageTpl::find();
    }
}
