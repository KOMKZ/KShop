<?php
namespace common\models\trans\ar;

use Yii;
use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
use common\models\pay\Currency;
use yii\behaviors\TimestampBehavior;
use common\models\set\SetModel;

/**
 *
 */
class Transaction extends ActiveRecord
{
    const STATUS_INIT = 'init';
    const STATUS_CANCEL = 'cancel';
    const STATUS_PAYED = 'payed';
    const STATUS_ERROR = 'error';

    CONST PAY_STATUS_PAYED = 'payed';
    const PAY_STATUS_NOPAY = 'nopay';

    const TYPE_CONSUME = 'consume';
    const TYPE_REFUND = 'refund';
    const TYPE_TRANSFER = 'transfer';

    const MODULE_ORDER = 'order';

    const EVENT_AFTER_PAYED = 'after_payed';

    public static function tableName(){
        return "{{%transaction}}";
    }


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 't_created_at',
                'updatedAtAttribute' => 't_updated_at'
            ]
        ];
    }

    public function rules(){
        return [
             ['t_succ_pay_type', 'default', 'value' => '']

            ,['t_pay_status', 'default', 'value' => self::PAY_STATUS_NOPAY]
            ,['t_pay_status', 'in', 'range' => ConstMap::getConst('t_pay_status', true)]

            ,['t_status', 'default', 'value' => self::STATUS_INIT]
            ,['t_status', 'in', 'range' => ConstMap::getConst('t_status', true)]

            ,['t_type', 'required']
            ,['t_type', 'in', 'range' => ConstMap::getConst('t_type', true)]

            ,['t_pay_at', 'integer']
            ,['t_pay_at', 'default', 'value' => 0]

            ,['t_invalid_at', 'integer']
            ,['t_invalid_at', 'default', 'value' => 0]

            ,['t_module', 'required']
            ,['t_module', 'in', 'range' => ConstMap::getConst('t_module', true)]

            ,['t_fee', 'required']
            ,['t_fee', 'integer', 'min' => 1]

            ,['t_fee_type', 'default', 'value' => Currency::CNY]
            ,['t_fee_type', 'in', 'range' => ConstMap::getConst('currency_type', true)]

            ,['t_app_no', 'required']

            ,['t_belong_uid', 'required']
            ,['t_belong_uid', 'integer']

            ,['t_create_uid', 'required']
            ,['t_create_uid', 'integer']

            ,['t_timeout', 'integer']
            ,['t_timeout', 'default', 'value' => SetModel::get('transaction.transaction_timeout') + time()]

            ,['t_title', 'required']
            ,['t_title', 'string']

            ,['t_content', 'string']
            ,['t_content', 'default', 'value' => '']
        ];
    }
}
