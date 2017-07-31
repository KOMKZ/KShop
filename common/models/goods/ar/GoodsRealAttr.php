<?php
namespace common\models\goods\ar;

use Yii;
use common\models\staticdata\ConstMap;
use yii\db\ActiveRecord;
use common\models\goods\query\GoodsAttrQuery;
use common\models\goods\query\GoodsOptionQuery;
use common\models\goods\ar\GoodsAttr;
/**
 *
 */
class GoodsRealAttr extends ActiveRecord
{
    const STATUS_VALID = 'valid';
    const STATUS_DELETE = 'delete';


    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $data = parent::toArray($fields, $expand, $recursive);
        $data = array_merge($data, $data['g_attr']);
        unset($data['g_attr']);
        return $data;
    }

    public static function tableName(){
        return "{{%goods_real_attr}}";
    }

    public function fields(){
        $fields = parent::fields();
        return array_merge($fields, [
            'g_atr_opts',
            'g_attr'
        ]);
    }
    public function getG_atr_opts(){
        return $this->hasMany(GoodsRealOption::className(), [
            'g_id' => 'g_id',
            'g_atr_id' => 'g_atr_id']);
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

            ['gr_status', 'default', 'value' => static::STATUS_VALID]

        ];
    }




}
