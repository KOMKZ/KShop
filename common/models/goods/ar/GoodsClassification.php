<?php
namespace common\models\goods\ar;

use Yii;
use common\models\staticdata\ConstMap;
use yii\db\ActiveRecord;
/**
 *
 */
class GoodsClassification extends ActiveRecord
{


    public static function tableName(){
        return "{{%goods_classification}}";
    }






    public function rules(){
        return [
            ['g_cls_name', 'string'],
            ['g_cls_name', 'required'],

            ['g_cls_show_name', 'string'],
            ['g_cls_show_name', 'default', 'value' => function(){return $this->g_cls_name;}],

            ['g_cls_pid', 'integer'],
            ['g_cls_pid', 'exist', 'targetAttribute' => 'g_cls_id'],
            ['g_cls_pid', 'default', 'value' => 0],



        ];
    }




}
