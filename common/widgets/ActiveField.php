<?php
namespace common\widgets;

use yii\bootstrap\ActiveField as BaseActiveField;
use yii\helpers\ArrayHelper;

class ActiveField extends BaseActiveField{
	public function arrayObjInput($options = []){
		$options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
		$options['model'] = $this->model;
		$options['attribute'] = $this->attribute;
        $this->parts['{input}'] = ArrayObjInput::widget($options);
        return $this;
	}
}
