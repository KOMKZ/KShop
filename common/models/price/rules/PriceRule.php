<?php
namespace common\models\price\rules;

use Yii;
use yii\base\Object;
use yii\base\Component;
use yii\base\Model;
use yii\base\ArrayableTrait;
/**
 *
 */
class PriceRule extends Model
{
    use ArrayableTrait;
    const TYPE_GLOBAL_ORDER_PRICE_DISCOUNT = 'global_order_price_discount';
    const TYPE_USER_COUPON_PRICE_DISCOUNT = 'user_coupon_price_discount';
    public function checkExistRule($otherDiscountData = []){
        return true;
    }

    public function getClass(){
        return static::className();
    }
    public function getId(){
        throw new \Exception(Yii::t('app', "id子类未定义"));
    }
    public function getDescription(){
        throw new \Exception(Yii::t('app', 'description子类未定义'));
    }
    public function getType(){
        throw new \Exception(Yii::t('app', "type子类未定义"));
    }
    public function getCanUse(){
        return $this->checkCanUse();
    }

}
