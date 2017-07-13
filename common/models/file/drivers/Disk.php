<?php
namespace common\models\file\drivers;

use Yii;
use common\models\Model;
use yii\base\InvalidConfigException;
use common\models\file\File;
use yii\helpers\FileHelper;
/**
 *
 */
class Disk extends Model implements SaveMediumInterface
{
    CONST NAME = 'disk';
    protected $base = '';
    public $dirMode = 0755;
    public $fileMode = 0755;

    public function setBase($value){
        if(!is_dir($value)){
            throw new InvalidConfigException(Yii::t('app',"{$value} 路径不存在"));
        }
        if(!is_writable($value)){
            throw new InvalidConfigException(Yii::t('app', "{$value} 对象没有写权限"));
        }
        $this->base = rtrim($value, '/');
    }

    public function save(File $file){
        $savePath = $this->base . '/' . $file->save_path;
        $saveDir = dirname($savePath);
        if(!is_dir($saveDir)){
            FileHelper::createDirectory($saveDir);
            chmod($saveDir, $this->dirMode);
        }
        copy($file->source_path, $savePath);
        chmod($savePath, $this->fileMode);
        $file->medium_info = $this->buildMediumInfo();
        return $file;
    }

    protected function buildMediumInfo(){
        return [
            'base' => $this->base,
            'dirMode' => $this->dirMode,
            'fileMode' => $this->fileMode
        ];
    }
}
