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

    public $g_cls_id = null;

    public $g_attrs = null;



    public static function tableName(){
        return "{{%goods}}";
    }

    public function fields(){
        $fields = parent::fields();
        return array_merge($fields, [
            'g_cls_id',
            'g_attrs',
        ]);
    }







    public function rules(){
        return [
            ['g_attrs', 'required'],

            ['g_cls_id', 'required'],
            ['g_cls_id', 'exist', 'targetAttribute' => 'g_cls_id', 'targetClass' => GoodsClassification::className()],

            ['g_status', 'required'],
            ['g_status', 'in', 'range' => ConstMap::getConst('g_status', true)],

            ['g_primary_name', 'string'],
            ['g_primary_name', 'required'],

            ['g_secondary_name', 'string'],
            ['g_secondary_name', 'default', 'value' => ''],

            ['g_status', 'in', 'range' => ConstMap::getConst('g_status', true)],
            ['g_status', 'default', 'value' => static::STATUS_DRAFT],

            // todo exist check
            ['g_create_uid', 'required'],

            ['g_updated_at', 'default', 'value' => time()],

            ['g_start_at', 'integer'],

            ['g_end_at', 'integer']

        ];
    }




}
