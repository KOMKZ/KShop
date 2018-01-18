<?php
namespace common\models\sms\ar;

use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
use common\models\sms\SmsModel;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 *
 */
class Sms extends ActiveRecord
{
    const PROVIDER_ALIDY = 'alidy';

    /**
     * 广播消息
     * @var string
     */
    const TYPE_BOARD = 'board';

    /**
     * 私信
     * @var string
     */
    const TYPE_PRIVATE = 'private';

    public $sms_params_object = [];

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'sms_created_at',
                'updatedAtAttribute' => false
            ]
        ];
    }

    public function getSms_real_outer_code(){
        $def = SmsModel::getInnerCodeMap($this->sms_inner_code);
        return $def['candicates'][$this->sms_provider];
    }

    public static function tableName(){
        return "hh_sms";
    }
    public function rules(){
        return [
            ['sms_provider', 'in', 'range' => ConstMap::getConst('sms_provider', true)],
            ['sms_provider', 'default', 'value' => self::PROVIDER_ALIDY],

            ['sms_type', 'required'],
            ['sms_type', 'in', 'range' => ConstMap::getConst('sms_type', true)],


            ['sms_inner_code', 'required'],
            ['sms_inner_code', 'in', 'range' => SmsModel::getInnerCodeMap()],
            ['sms_inner_code', 'checkValidInnerCode'],


            ['sms_params_object', 'safe']
        ];
    }

    public function checkValidInnerCode($attr){
        $def = SmsModel::getInnerCodeMap($this->sms_inner_code);
        if(empty($def) || !is_array($def) || !array_key_exists('candicates', $def)){
            $this->addError('sms_inner_code', Yii::t('app', '无效的短信业务码'));
            return false;
        }
        if(!array_key_exists($this->sms_provider, $def['candicates'])){
            $this->addError('sms_inner_code', Yii::t('app', '短信业务码没有对应的短信服务商'));
            return false;
        }
        return true;
    }
}
