<?php
namespace common\models\price\rules;

use yii\base\Object;
/**
 *
 */
class OrderPriceRule extends PriceRule
{
    public $order = null;
    public function __construct($config = []){
        if(!is_object($this->order)){
            throw new \InvalidConfigException(Yii::('app', "OrderPriceRule中order属性不能为空"));
        }
        parent::__construct($config);
    }
}
