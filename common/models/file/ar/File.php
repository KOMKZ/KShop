<?php
namespace common\models\file\ar;

use Yii;
use common\models\Model;
use common\helpers\FileHelper;
use common\models\file\FileModel;
use common\models\file\drivers\Disk;
use common\models\staticdata\ConstMap;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
/**
 *
 */
class File extends ActiveRecord
{
    public $file_source_path = '';



    public function getFile_query_id(){
        return FileModel::buildFileQueryId($this);
    }

    public function getFile_url(){
        return FileModel::buildFileUrl($this);
    }

    public function getFileSavePath(){
        return FileModel::buildFileSavePath($this);
    }


    public function fields(){
        $attrs = array_merge(parent::attributes(), [
            'file_query_id',
            'file_url'
        ]);
        $extra = ['file_source_path'];
        foreach($extra as $attr){
            ArrayHelper::removeValue($attrs, $attr);
        }
        return $attrs;
    }



    public function rules(){
        return [
            ['file_is_private', 'default', 'value' => 0],
            ['file_is_private', 'integer'],
            ['file_is_private', 'in', 'range' => ConstMap::getConst('file_is_private', true)],

            ['file_is_tmp', 'default', 'value' => 1],
            ['file_is_tmp', 'integer'],
            ['file_is_tmp', 'in', 'range' => ConstMap::getConst('file_is_tmp', true)],


            ['file_save_name', 'string'],
            ['file_save_name', 'filter', 'filter' => function($value){
                return FileHelper::buildFileSafeName($value);
            }],

            ['file_save_type', 'default', 'value' => Disk::NAME],
            ['file_save_type', 'in', 'range' => ConstMap::getConst('file_save_type', true)],

            ['file_valid_time', 'default', 'value' => 0],
            ['file_valid_time', 'integer'],

            ['file_source_path', 'string'],
            ['file_source_path', function($attr){
                if(!file_exists($this->$attr)){
                    $this->addError('source_path', Yii::t('app', "{$this->$attr} 文件不存在"));
                }
            }],

            ['file_category', 'string'],
            ['file_category', 'filter', 'filter' => function($value){
                return trim($value, '/');
            }]
        ];
    }




}
