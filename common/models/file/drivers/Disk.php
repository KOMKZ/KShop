<?php
namespace common\models\file\drivers;

use Yii;
use common\models\Model;
use yii\base\InvalidConfigException;
use common\models\file\ar\File;
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
    public $host = "";
    public $urlRoute = "";

    public function setBase($value){
        if(!is_dir($value)){
            throw new InvalidConfigException(Yii::t('app',"{$value} 路径不存在"));
        }

        if(!is_writable($value)){
            throw new InvalidConfigException(Yii::t('app', "{$value} 对象没有写权限"));
        }
        $this->base = rtrim($value, '/');
    }

    public function buildFileUrl(File $file, $params = []){
        $apiUrlManager = Yii::$app->apiurl;
        $apiUrlManager->hostInfo = $this->host;
        return $apiUrlManager->createAbsoluteUrl([$this->urlRoute, 'query_id' => $file->file_query_id]);
    }
    public function saveByCopy(File $targetFile, File $originFile){
        return $targetFile;
    }
    public function save(File $file){
        $savePath = $this->base . '/' . $file->getFileSavePath();
        $saveDir = dirname($savePath);
        if(!is_dir($saveDir)){
            FileHelper::createDirectory($saveDir);
            chmod($saveDir, $this->dirMode);
        }
        copy($file->file_source_path, $savePath);
        chmod($savePath, $this->fileMode);
        return $file;
    }

    public function buildMediumInfo(){
        return [
            'base' => $this->base,
            'dirMode' => $this->dirMode,
            'fileMode' => $this->fileMode
        ];
    }
}
