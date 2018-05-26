<?php
namespace common\models\goods\ar;

use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
use common\models\file\FileModel;

/**
 *
 */
class GoodsSource extends ActiveRecord
{
    const CLS_TYPE_SKU = 'sku';
    const CLS_TYPE_OPTION = 'option';
    const CLS_TYPE_GOODS = 'goods';

    const TYPE_IMG = 'img';

    const U_SKU_M_IMG = 'sku_m';
    const U_GOODS_M_IMG = 'goods_m';




    public static function tableName(){
        return "{{%goods_source}}";
    }



    public function fields(){
        $fields = parent::fields();
        return array_merge($fields, [
            'gs_content',
        ]);
    }

    public function getGs_content(){
        if(static::TYPE_IMG == $this->gs_type){
            $fileInfo = FileModel::parseQueryId($this->gs_sid);
            $url = FileModel::buildFileUrlStatic($fileInfo);
            return $url;
        }
        return '';
    }

    public function rules(){
        return [
            ['gs_type', 'required'],
            ['gs_type', 'in', 'range' => ConstMap::getConst('gs_type', true)],

            ['gs_use_type', 'required'],
            ['gs_use_type', 'in', 'range' => ConstMap::getConst('gs_use_type', true)],

            ['gs_sid', 'required'],

            ['g_id', 'required'],
            ['g_id', 'integer'],

            ['gs_name', 'string'],


        ];
    }
}
