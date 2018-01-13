<?php
namespace common\models\goods\ar;

use Yii;
use common\models\staticdata\ConstMap;
use yii\db\ActiveRecord;
use common\models\goods\query\GoodsAttrQuery;
use common\models\file\FileModel;
/**
 *
 */
class GoodsRealOption extends ActiveRecord
{
    public static function tableName(){
        return "{{%goods_real_option}}";
    }

    public function fields(){
		$fields = parent::fields();
		return array_merge($fields, [
			'g_opt_img_url'
		]);
	}

    public function getG_opt_img_url(){
        if($this->g_opt_img){
            $queryId = FileModel::parseQueryId($this->g_opt_img);
            if($queryId){
                return FileModel::buildFileUrlStatic($queryId);
            }else{
                return $this->g_opt_img;
            }
        }
        return '';
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
