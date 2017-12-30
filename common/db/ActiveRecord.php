<?php
namespace common\db;
use yii\db\ActiveRecord as Base;
use yii\helpers\ArrayHelper;
/**
 * 
 */
class ActiveRecord extends Base
{
	public function toArray(array $fields = [], array $expand = [], $recursive = true)
	{
		$data = parent::toArray($fields, $expand, $recursive);
		if(empty($this->releaseFields())){
			return $data;
		}
		// bug: 可能导致空字段无法合并
		foreach($this->releaseFields() as $field){
			$releaseData = ArrayHelper::getValue($data, $field, []);
			if($releaseData){
				$data = array_merge($data, $releaseData);
				unset($data[$field]);
			}
		}
		return $data;
	}
	public function releaseFields(){
		return [];
	}
}
