<?php
namespace common\models\pay\ar;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\staticdata\ConstMap;
use yii\helpers\ArrayHelper;

/**
 *
 */
class PayTrace extends ActiveRecord
{

    const STATUS_INIT = 'init';
    const STATUS_CANCEL = 'cancel';
    const STATUS_PAYED = 'payed';
    const STATUS_ERROR = 'error';

    CONST PAY_STATUS_PAYED = 'payed';
    const PAY_STATUS_NOPAY = 'nopay';

    CONST TYPE_DATA = 'data';
    CONST TYPE_URL = 'url';

    CONST EVENT_AFTER_PAYED = "after_payed";


    public static function tableName(){
        return "{{%pay_trace}}";
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'pt_created_at',
                'updatedAtAttribute' => 'pt_updated_at'
            ]
        ];
    }

    public function setThird_data(Array $value){
        $thirdData = ($thirdData = json_decode($this->pt_third_data, true)) ? $thirdData : [];
        $thirdData = ArrayHelper::merge($thirdData, $value);
        $this->pt_third_data = json_encode($thirdData);
    }

    public function rules(){
        return [
            ['pt_pay_type', 'required']
            ,['pt_pay_type', 'in', 'range' => ConstMap::getConst('payment', true)]

            ,['pt_pre_order_type', 'required']
            ,['pt_pre_order_type', 'in', 'range' => ConstMap::getConst('pt_pre_order_type', true)]

            ,['pt_pay_status', 'required']
            ,['pt_pay_status', 'in', 'range' => ConstMap::getConst('pt_pay_status', true)]

            ,['pt_status', 'required']
            ,['pt_status', 'in', 'range' => ConstMap::getConst('pt_status', true)]

            ,['pt_belong_trans_number', 'required']

            ,['pt_third_data', 'default', 'value' => '']

            ,['pt_timeout', 'required']
            ,['pt_timeout', 'integer']


        ];
    }
}
