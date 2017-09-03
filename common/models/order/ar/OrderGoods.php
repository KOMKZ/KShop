<?php
namespace common\models\order\ar;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 *
 */
class OrderGoods extends ActiveRecord
{
    public static function tableName(){
        return "{{%order_goods}}";
    }

    public function rules(){
        return [];
    }


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'og_created_at',
                'updatedAtAttribute' => 'og_updated_at'
            ]
        ];
    }
}
