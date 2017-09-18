<?php
namespace common\models\user\ar;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\models\staticdata\ConstMap;
use yii\helpers\ArrayHelper;
use common\models\user\query\UserQuery;
use common\models\set\SetModel;
use common\models\user\UserModel;
use Firebase\JWT\JWT;
use yii\behaviors\TimestampBehavior;

/**
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
	const STATUS_ACTIVE = 'active';

	const STATUS_NO_AUTH = 'not_auth';

	const NOT_AUTH = 'not_auth';

	const HAD_AUTH = 'had_auth';

	public $password;

	public $password_confirm;

	public $rememberMe = false;

	public function behaviors()
	{
		return [
			[
				'class' => TimestampBehavior::className(),
				'createdAtAttribute' => 'u_created_at',
				'updatedAtAttribute' => 'u_updated_at'
			]
		];
	}

	public function fields(){
		$fields = parent::fields();
		ArrayHelper::removeValue($fields, 'u_password_hash');
		ArrayHelper::removeValue($fields, 'u_auth_key');
		ArrayHelper::removeValue($fields, 'u_password_reset_token');
		ArrayHelper::removeValue($fields, 'u_access_token');
		return $fields;
	}

	public function rules(){
		return [
			['u_username', 'required'],
			['u_username', 'match', 'pattern' => '/[a-zA-Z0-9_\-]/'],
			['u_username', 'string', 'min' => 5, 'max' => 30],
			['u_username', 'unique', 'targetClass' => self::className()],

			['u_email', 'required'],
			['u_email', 'email'],
			['u_email', 'unique', 'targetClass' => self::className()],

			['u_status', 'required'],
			['u_status', 'in', 'range' => ConstMap::getConst('u_status', true)],

			['u_auth_status', 'default', 'value' => User::STATUS_NO_AUTH],
			['u_auth_status', 'in', 'range' => ConstMap::getConst('u_auth_status', true)],

			['password', 'required', 'on' => 'create'],
			['password', 'required', 'on' => 'update', 'skipOnEmpty' => true],

			['u_access_token', 'default', 'value' => ''],

			['password', 'string', 'min' => 6, 'max' =>  50],

			['password_confirm', 'required', 'on' => 'create'],
			['password_confirm', 'required', 'on' => 'update', 'skipOnEmpty' => true],
			['password_confirm', 'compare', 'compareAttribute' => 'password'],

		];
	}

	public static function tableName(){
		return "{{%user}}";
	}

	public static function findIdentity($id)
	{
		return UserQuery::findActive()->andWhere(['=', 'u_id', $id]);
	}

	public static function findIdentityByAccessToken($token, $type = null)
	{
		try {
			$payload = UserModel::parseAccessToken($token, $type);
			$user = UserQuery::findActive()->andWhere(['=', 'u_email', $payload->data->user_info->u_email])->one();
			if($user->u_access_token != $payload->jti){
				return null;
			}
			return $user;
		} catch (\Exception $e) {
			return null;
		}
	}

	public static function findByUsername($username)
	{
		return static::findOne(['u_username' => $username, 'u_status' => self::STATUS_ACTIVE]);
	}

	public static function findByPasswordResetToken($token)
	{
		if (!static::isPasswordResetTokenValid($token)) {
			return null;
		}
		return static::findOne([
			'u_password_reset_token' => $token,
			'u_status' => self::STATUS_ACTIVE,
		]);
	}
	public function getId()
	{
		return $this->getPrimaryKey();
	}

	public function getAuthKey()
	{
		return $this->u_auth_key;
	}

	public function validateAuthKey($authKey)
	{
		return $this->getAuthKey() === $authKey;
	}

}
