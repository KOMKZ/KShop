<?php
namespace common\models\goods\ar;

use Yii;
use common\models\staticdata\ConstMap;
use yii\db\ActiveRecord;

/**
 *
 */
class GoodsMeta extends ActiveRecord{
    const STATUS_VALID = 'valid';
    const STATUS_DELETE = 'delete';
    public static function tableName(){
        return "{{%goods_meta}}";
    }
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $data = parent::toArray($fields, $expand, $recursive);
        $data = array_merge($data, $data['g_attr']);
        unset($data['g_attr']);
        return $data;
    }
    public function fields(){
        $fields = parent::fields();
        return array_merge($fields, [
            'g_attr'
        ]);
    }
    public function getG_attr(){
        return $this->hasOne(GoodsAttr::className(), [
            'g_atr_id' => 'g_atr_id'
        ]);
    }

    public function rules(){
        return [

            ['g_atr_id', 'required'],

            ['g_id', 'required'],

            ['gm_value', 'required'],

            ['gm_status', 'default', 'value' => static::STATUS_VALID]

        ];
    }
}
