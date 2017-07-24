<?php
namespace common\models\goods\ar;

use Yii;
use common\models\staticdata\ConstMap;
use common\models\goods\ar\GoodsClassification;
use yii\db\ActiveRecord;
/**
 *
 */
class GoodsAttr extends ActiveRecord
{


    public static function tableName(){
        return "{{%goods_attr}}";
    }






    public function rules(){
        return [
            ['g_atr_code', 'string'],
            ['g_atr_code', 'required'],

            ['g_atr_name', 'string'],
            ['g_atr_name', 'required'],

            ['g_atr_show_name', 'string'],
            ['g_atr_show_name', 'default', 'value' => function(){return $this->g_atr_name;}],

            ['g_atr_cls_id', 'integer'],
            ['g_atr_cls_id', 'exist', 'targetAttribute' => 'g_cls_id', 'targetClass' => GoodsClassification::className()],


        ];
    }




}
