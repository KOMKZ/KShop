<?php
namespace common\models\price\rules;

use Yii;
use common\models\price\rules\OrderPriceRule;
use common\models\price\rules\PriceRuleInterface;
use yii\base\InvalidConfigException;

/**
 *
 */
class OrderExemptExpressFee extends OrderPriceRule implements PriceRuleInterface
{
    public $fullValue = null;
    public $priceName = "元";
    public $autoShow = false;
    public function __construct($config = []){
        parent::__construct($config);
        $this->validate();
    }
    public function getNewPrice(){
        return $this->originPrice - $this->sliceValue;
    }
    public function getId(){
        return "order_exempt_express_fee";
    }
    public function getDescription(){
        return sprintf("满%s(%s)免快递费",
            $this->fullValue/100,
            $this->priceName
        );
    }
    public function getType(){
        return self::TYPE_GLOBAL_ORDER_PRICE_DISCOUNT;
    }
    public function checkCanUse(){
        return $this->originPrice >= $this->fullValue;
    }

    public function validate(){
        if(
            null === $this->fullValue
        ){
            throw new InvalidConfigException(Yii::t('app', "参数配置不正确"));
        }
    }
}
