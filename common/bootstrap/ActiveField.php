<?php
namespace common\bootstrap;

use yii\bootstrap\ActiveField as BaseActiveField;
use yii\helpers\ArrayHelper;

class ActiveField extends BaseActiveField{
	public function arrayObjInput(){
		return "hello world";
	}
}
