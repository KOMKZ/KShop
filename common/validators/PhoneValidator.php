<?php
namespace common\validators;

use Yii;
use yii\validators\Validator;
use yii\base\InvalidConfigException;


class PhoneValidator extends Validator
{


    public function init(){
        parent::init();
        $this->message = Yii::t('app', sprintf('value of {attribute} {value} is invalid.'));
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        return preg_match('/^1[34578]\d{9}$/', $value) ? null : [$this->message, []];
    }

}
