<?php
namespace common\models\price\rules;

use Yii;
use common\models\price\rules\OrderPriceRule;
use common\models\price\rules\PriceRuleInterface;
use yii\base\InvalidConfigException;
use common\models\price\rules\OrderCouponSlice;

/**
 *
 */
class OrderFullSliceRule extends OrderPriceRule implements PriceRuleInterface
{
    public $fullValue = null;
    public $priceName = "元";
    public $checkExist = true;
    public function __construct($config = []){
        parent::__construct($config);
        $this->validate();
    }
    public function checkExistRule($otherDiscountData){
        // 互斥性检查应该可以定制话或者配置话， 不能写死在代码todo
        foreach($otherDiscountData as $discount){
            if($discount instanceof OrderCouponSlice){
                throw new \Exception(Yii::t('app', "满减优惠不能和优惠券同时使用"));
            }
        }
    }
    public function getId(){
        return "order_full_slice";
    }
    public function getNewPrice(){
        return $this->originPrice - $this->sliceValue;
    }
    public function getDescription(){
        return sprintf("满%s(%s)减%s(%s)%s",
            $this->fullValue/100,
            $this->priceName,
            $this->sliceValue/100,
            $this->priceName,
            !$this->checkExist ? ',不与优惠券同时使用' : ''
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
            null === $this->fullValue ||
            null == $this->sliceValue
        ){
            throw new InvalidConfigException(Yii::t('app', "参数配置不正确"));
        }
    }
    public function fields(){
        return array_merge(parent::fields(), [
            'newPrice'
        ]);
    }
}
