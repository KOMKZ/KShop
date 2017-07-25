<?php
namespace common\models\goods\ar;

use Yii;
use common\models\staticdata\ConstMap;
use common\models\goods\ar\GoodsClassification;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 *
 */
class Goods extends ActiveRecord
{
    CONST STATUS_DRAFT = 'draft';
    CONST STATUS_ON_SALE = 'on_sale';
    CONST STATUS_ON_NOT_SALE = 'on_not_sale';
    CONST STAUTS_FORBIDDEN = 'forbidden';


    public static function tableName(){
        return "{{%goods}}";
    }


    public function behaviors(){
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'g_created_at',
                'updatedAtAttribute' => 'g_updated_at'
            ]
        ];
    }



    public function rules(){
        return [
            ['g_primary_name', 'string'],
            ['g_primary_name', 'requird'],

            ['g_secondary_name', 'string'],
            ['g_secondary_name', 'default', 'value' => ''],

            ['g_status', 'in', 'range' => ConstMap::getConst('g_status')],
            ['g_status', 'default', 'value' => static::STATUS_DRAFT],

            // todo exist check
            ['g_create_uid', 'required'],
            // todo exist check
            ['g_update_uid', 'required'],

        ];
    }




}
