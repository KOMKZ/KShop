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

    public function rules(){
        return [
            ['g_id', 'required'],

            ['g_atr_id', 'required'],

            ['g_opt_name', 'required'],

            ['g_opt_img', 'string'],
            ['g_opt_img', 'default', 'value' => '']
        ];
    }






}
