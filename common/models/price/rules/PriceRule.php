<?php
namespace common\models\price\rules;

use Yii;
use yii\base\Object;
/**
 *
 */
class PriceRule extends Object
{
    public function getName(){
        return static::calssName();
    }
    public function getDescription(){
        throw new \Exception(Yii::t('app', '未定义'));
    }
}
