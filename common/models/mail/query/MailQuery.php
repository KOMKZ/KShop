<?php
namespace common\models\mail\query;

use yii\base\Object;
use common\models\mail\ar\Mail;

/**
 *
 */
class MailQuery extends Object
{
    public static function find(){
        return Mail::find();
    }
}
