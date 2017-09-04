<?php
namespace common\models\price\rules;

use common\models\price\rules\OrderPriceRule;
use common\models\price\rules\PriceRuleInterface;

/**
 *
 */
class OrderFullSliceRule extends OrderPriceRule implements PriceRuleInterface
{
    public $fullValue = null;
    public $sliceValue = null;
    public $priceName = "元";

    public function getFinalPrice(){
        return $this->order->origin_price - $this->sliceValue;
    }
    public function getDescription(){
        return sprintf("满%s(%s)减%s(%s)",
        $this->fullValue,
        $this->priceName,
        $this->sliceValue,
        $this->priceName
    );
    }
    public function checkCanUse(){
        return $this->order->origin_price >= $this->fullValue;
    }

    public static function validate($data){
        if(
            null === $this->fullValue ||
            null == $this->sliceValue
        ){
            return false;
        }
        return true;
    }
}
