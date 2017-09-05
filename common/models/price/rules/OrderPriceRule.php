<?php
namespace common\models\price\rules;

use Yii;
use yii\base\Object;
use yii\base\InvalidConfigException;
use yii\base\ArrayableTrait;
/**
 *
 */
class OrderPriceRule extends PriceRule
{
    protected $order = null;
    public $originPrice = null;
    public $autoShow = true;
    public function setOrder($value){
        $this->order = $value;
    }
    public function __construct($config = []){
        parent::__construct($config);
        if(!is_object($this->order)){
            throw new InvalidConfigException(Yii::t('app', "OrderPriceRule中order属性不能为空"));
        }
        if(null === $this->originPrice){
            throw new InvalidConfigException(Yii::t('app', "OrderPriceRule中originPrice属性不能为空"));
        }
    }
    public function fields(){
        return array_merge(parent::fields(), [
            'name',
            'autoShow',
            'description',
            'type',
            'id',
            'canUse'
        ]);
    }
}
