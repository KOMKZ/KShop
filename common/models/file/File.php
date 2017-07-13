<?php
namespace common\models\file;

use Yii;
use common\models\Model;
use common\helpers\FileHelper;
use common\models\file\FileModel;
use common\models\file\drivers\Disk;
use common\models\staticdata\ConstMap;
/**
 *
 */
class File extends Model
{
    public $is_private = 0;
    public $is_tmp = 0;
    public $save_name = '';
    public $save_type = '';
    public $valid_time = 0;
    public $source_path = '';
    public $save_path = '';
    public $category = '';
    public $prefix = '';
    public $ext = '';
    public $md5_value = '';
    public $medium_info = [];

    public function getQuery_id(){
        return FileModel::buildFileQueryId($this);
    }

    public function getUrl(){
        return FileModel::buildFileUrl($this);
    }


    public function attributes(){
        return array_merge(parent::attributes(), [
            'query_id',
            'url'
        ]);
    }



    public function rules(){
        return [
            ['is_private', 'integer'],
            ['is_private', 'in', 'range' => ConstMap::getConst('file_is_private', true)],


            ['is_tmp', 'integer'],
            ['is_tmp', 'in', 'range' => ConstMap::getConst('file_is_tmp', true)],


            ['save_name', 'string'],
            ['save_name', 'filter', 'filter' => function($value){
                return FileHelper::buildFileSafeName($value);
            }],

            ['save_type', 'in', 'range' => ConstMap::getConst('file_save_type', true)],

            ['valid_time', 'integer'],

            ['source_path', 'string'],
            ['source_path', function($attr){
                if(!file_exists($this->$attr)){
                    $this->addError('source_path', Yii::t('app', "{$this->$attr} 文件不存在"));
                }
            }],

            ['category', 'string'],
            ['category', 'filter', 'filter' => function($value){
                return trim($value, '/');
            }]
        ];
    }




}
