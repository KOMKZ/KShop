<?php
namespace common\models\price\rules;

use Yii;
use common\models\price\rules\OrderPriceRule;
use common\models\price\rules\PriceRuleInterface;
use yii\base\InvalidConfigException;

/**
 *
 */
class OrderCouponSlice extends OrderPriceRule implements PriceRuleInterface
{
    public $fullValue = null;
    public $priceName = "元";
    public $couponCode = null;
    public $beginAt = null;
    public $endAt = null;
    private $_id = null;

    public static function buildCouponNumber(){
        list($time, $millsecond) = explode('.', microtime(true));
        $string = sprintf("CR%s%04d", date("HYisdm", $time), $millsecond);
        return $string;
    }

    public function __construct($config = []){
        parent::__construct($config);
        $this->validate();
    }
    public function getNewPrice(){
        return $this->originPrice - $this->sliceValue;
    }
    public function getId(){
        return $this->_id;
    }
    public function setId($value){
        $this->_id = $value;
    }
    public function getDescription(){
        return sprintf("优惠券:满%s(%s)减%s(%s)",
            $this->fullValue/100,
            $this->priceName,
            $this->sliceValue/100,
            $this->priceName
        );
    }
    public function getType(){
        return self::TYPE_USER_COUPON_PRICE_DISCOUNT;
    }
    public function checkCanUse(){
        return $this->originPrice >= $this->fullValue;
    }

    public function validate(){
        if(
            null === $this->fullValue ||
            null == $this->sliceValue ||
            null == $this->couponCode ||
            null == $this->beginAt ||
            null == $this->endAt
        ){
            throw new InvalidConfigException(Yii::t('app', "参数配置不正确"));
        }
    }

    public function fields(){
        return array_merge(parent::fields(), [
            'newPrice',
            'couponCode'
        ]);
    }
}
