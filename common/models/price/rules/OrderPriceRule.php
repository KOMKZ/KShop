<?php
namespace common\models\price\rules;

use Yii;
use yii\base\Object;
use yii\base\InvalidConfigException;
/**
 *
 */
class OrderPriceRule extends PriceRule
{
    public $order = null;
    public function __construct($config = []){
        parent::__construct($config);
        if(!is_object($this->order)){
            throw new InvalidConfigException(Yii::t('app', "OrderPriceRule中order属性不能为空"));
        }
    }
}
