<?php
namespace common\models\file;

use Yii;
use common\models\Model;
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
    public $category = '';
    public function rules(){
        return [
            ['is_private', 'integer'],

            ['is_tmp', 'integer'],

            ['save_name', 'string'],

            ['save_type', 'in', 'range' => ConstMap::getConst('file_save_type', true)],

            ['valid_time', 'integer'],

            ['source_path', 'string'],

            ['category', 'string']
        ];
    }
    // public $is_private = 0;
    // public $is_tmp = 0;
    // public $save_name = '';
    //
    //
    // private $_save_type = Disk::NAME;
    // public function setSave_type($value){
    //     $this->_save_type = $value;
    //     if(!in_array($value, ConstMap::getConst('file_save_type', true))){
    //         $this->addError('', Yii::t('app', "{$value} 不在枚举值范围内"));
    //     }
    // }
    // public function getSave_type(){
    //     return $this->_save_type;
    // }
    //
    //
    // public $valid_time = 0;
    // private $_source_path = '';
    // public function setSource_path($value){
    //     $this->_source_path = rtrim($value, '/');
    //     if(!file_exists($this->_source_path)){
    //         $this->addError('', Yii::t('app', "{$value} 文件路径不存在"));
    //     }
    // }
    // public function getSource_path(){
    //     return $this->_source_path;
    // }
    //
    //
    // private $_category = '';
    // public function setCategory($value){
    //     $this->_category = trim($value, '/');
    // }
    // public function getCategory(){
    //     return $this->_category;
    // }
    //
    //
    // public function attributes(){
    //     return array_merge(parent::attributes(), [
    //         'category',
    //         'source_path',
    //         'save_type'
    //     ]);
    // }


}
