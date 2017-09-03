<?php
namespace common\validators;

use Yii;
use yii\validators\Validator;
use yii\base\InvalidConfigException;


class TypeValidator extends Validator
{
    public $expectType = null;

    const TYPE_ARRAY = 'array';

    public function init(){
        parent::init();
        if(null === $this->expectType){
            throw new InvalidConfigException(Yii::t('app', "必须指定验证的类型"));
        }
        $this->message = Yii::t('app', sprintf('type of {attribute} must be equal to "%s".', $this->expectType));
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        switch ($this->expectType) {
            case self::TYPE_ARRAY:
                if(is_array($value)){
                    return null;
                }
                return [$this->message, []];
                break;
            default:
                throw new InvalidConfigException(Yii::t('app', "不合法的expectType值{$this->expectType}"));
                break;
        }
    }

}
