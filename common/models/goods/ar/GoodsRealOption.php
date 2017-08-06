<?php
namespace common\models\goods\ar;

use Yii;
use common\models\staticdata\ConstMap;
use yii\db\ActiveRecord;
use common\models\goods\query\GoodsAttrQuery;
/**
 *
 */
class GoodsRealOption extends ActiveRecord
{
    public static function tableName(){
        return "{{%goods_real_option}}";
    }


    public function scenarios(){
        return [
            'default' => [
                'g_id', 'g_atr_id', 'g_opt_name', 'g_opt_value', 'g_opt_img'
            ],
            'update' => [
                'g_opt_name', 'g_opt_value', 'g_opt_img'
            ]
        ];
    }

    public function rules(){
        return [
            ['g_id', 'required'],

            ['g_atr_id', 'required'],

            ['g_opt_name', 'required'],

            ['g_opt_value', 'required'],

            ['g_opt_img', 'string'],
            ['g_opt_img', 'default', 'value' => '']
        ];
    }






}
