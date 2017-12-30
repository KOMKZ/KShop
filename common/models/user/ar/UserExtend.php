<?php
namespace common\models\user\ar;

use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
use yii\behaviors\TimestampBehavior;

/**
 *
 */
class UserExtend extends ActiveRecord
{
	public static function tableName(){
		return "{{%user_extend}}";
	}
	public function behaviors()
	{
		return [
			[
				'class' => TimestampBehavior::className(),
				'createdAtAttribute' => 'u_ext_created_at',
				'updatedAtAttribute' => 'u_ext_updated_at'
			]
		];
	}
	public function rules(){
		return [
			['u_avatar_id1', 'string']
			,['u_avatar_id1', 'default', 'value' => '']
			
			,['u_avatar_id2', 'string']
			,['u_avatar_id2', 'default', 'value' => '']
		];
	}
}
