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

    // 这些字段可用于筛选分类，如商品的轮播图
    const TYPE_IMG = 'img';


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

            ['gs_sid', 'required'],

            ['gs_name', 'string'],


        ];
    }
}
