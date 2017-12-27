<?php
namespace common\models\staticdata;

use Yii;
use yii\base\Object;
use yii\base\InvalidParamException;


/**
 *
 */
class ConstMap extends Object
{
	public static $map = [];
	public static function getConst($name = null, $onlyValue = false){
		if(empty(self::$map)){
			self::$map = require(Yii::getAlias("@common/models/staticdata/data/const_map.php"));
		}
		if(!$name){
			return self::$map;
		}
		if(!array_key_exists($name, self::$map)){
			throw new InvalidParamException("{$name} 不存在");
		}
		return $onlyValue ? array_keys(self::$map[$name]) : self::$map[$name];
	}
	public static function getSchemas(){
		$schemas = [
			\common\models\user\ar\User::className() => [
				'safe' => ['u_password_hash', 'u_password_reset_token', 'u_access_token', 'u_auth_key']
			]
		];
		$class = \common\models\user\ar\User::className();
		console($class::getTableSchema());
	}
}
