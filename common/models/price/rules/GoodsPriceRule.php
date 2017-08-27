<?php
namespace common\models\price\rules;

use yii\base\Object;
/**
 *
 */
class GoodsPriceRule extends PriceRule
{
    public $goodsSku = null;
    public function __construct($config = []){
        if(!is_object($this->goodsSku)){
            throw new \InvalidConfigException(Yii::('app', "OrderPriceRule中goodsSku属性不能为空"));
        }
        parent::__construct($config);
    }
}
