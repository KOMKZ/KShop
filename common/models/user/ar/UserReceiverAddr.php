<?php
namespace common\models\user\ar;

use Yii;
use yii\db\ActiveRecord;
use common\helpers\ChinaAreaHelper;
use yii\behaviors\TimestampBehavior;
use common\models\user\query\UserReceiverAddrQuery;

/**
 *
 */
class UserReceiverAddr extends ActiveRecord
{
    public static function tableName(){
        return "{{%user_receiver_addr}}";
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'rece_created_at',
                'updatedAtAttribute' => null
            ]
        ];
    }

    public function rules(){
        return [
            ['rece_name', 'required']
            ,['rece_name', 'string']

            ,['rece_status', 'default', 'value' => 'valid']
            ,['rece_status', 'in', 'range' => ['valid', 'delete']]

            ,['rece_contact_number', 'required']
            ,['rece_contact_number', 'string']

            ,['rece_location_id', 'required']
            ,['rece_location_id', function($attr){
                if(!ChinaAreaHelper::validateAreaId($this->$attr)){
                    $this->addError('rece_location_id', Yii::t('app', "地址信息不合法{$this->$attr}"));
                }
            }]

            ,['rece_location_string', 'required']
            ,['rece_location_string', 'string']

            ,['rece_tag', 'default', 'value' => '']
            ,['rece_tag', 'string']

            

        ];
    }
}
