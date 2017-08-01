<?php
namespace common\models\goods\ar;

use yii\db\ActiveRecord;

/**
 *
 */
class GoodsDetail extends ActiveRecord
{
    public static function tableName(){
        return "{{%godos_detail}}";
    }
    public function rules(){
        return [
            ['g_id', 'required'],

            ['g_intro_text', 'required'],
            ['g_intro_text', 'string']
        ];
    }
    
}
