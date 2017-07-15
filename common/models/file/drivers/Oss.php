<?php
namespace common\models\file\drivers;

use Yii;
use common\models\Model;
use yii\base\InvalidConfigException;
use common\models\file\ar\File;
use yii\helpers\FileHelper;
use OSS\OssClient;

/**
 *
 */
class Oss extends Model implements SaveMediumInterface{
    CONST NAME = 'oss';

    public $bucket;
    public $access_key_id;
    public $access_secret_key;
    protected $is_cname;
    public $endpoint;
    public $inner_endpoint;
    protected $base;
    public $timeout = 3600;

    protected static $oss;
    protected static $innerOss;

    public function setBase($value){
        $this->base = trim($value, '/');
    }
    public function setIs_cname($value){
        $this->is_cname = (boolean)$value;
    }

    public function init(){
        parent::init();
        $requireAttrs = ['bucket', 'access_key_id', 'access_secret_key', 'endpoint', 'inner_endpoint', 'base'];
        foreach($requireAttrs as $attr){
            if(empty($this->$attr)){
                throw new InvalidConfigException(Yii::t('app', "{$attr}不能为空"));
            }
        }
    }

    public function buildFileUrl(File $file, $params = []){
        if(!$file->file_is_private){
            $host = $this->getHostName();
            $objectName = $this->buildFileObjectName($file);
            return "http://" . $host . '/' . $objectName;
        }else{
            $objectName = $this->buildFileObjectName($file);
            return $this->createClient()->signUrl($this->bucket, $objectName, $this->timeout , OssClient::OSS_HTTP_GET, [
                OssClient::OSS_HEADERS => [
                    Oss::OSS_CONTENT_DISPOSTION => $file->file_save_name
                ]
            ]);
        }
    }

    protected function getHostName($inner = false){
        if($inner){
            return implode('.', [$this->bucket, $this->inner_endpoint]);
        }else{
            if($this->is_cname){
                return $this->endpoint;
            }else{
                return implode('.', [$this->bucket, $this->endpoint]);
            }
        }
    }
    public function buildFileObjectName(File $file){
         return $this->base . '/' . $file->getFileSavePath();
    }
    public function save(File $file){
        $client = $this->createClient(true);
        $objectName = $this->buildFileObjectName($file);
        $options = [];
        $options[OssClient::OSS_CONTENT_LENGTH] = filesize($file->file_source_path);
        $client->uploadFile($this->bucket, $objectName, $file->file_source_path, $options);
        if(!$file->file_is_private){
            $client->putObjectAcl($this->bucket, $objectName, OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE);
        }
        $file->file_medium_info = json_encode($this->buildMediumInfo());
        return $file;
    }

    public function createClient($inner = false){
        if(!$inner){
            if(null === self::$oss){
                self::$oss = new OssClient($this->access_key_id, $this->access_secret_key, $this->endpoint, $this->is_cname);
            }
            return self::$oss;
        }else{
            if(null === self::$innerOss){
                self::$innerOss = new OssClient($this->access_key_id, $this->access_secret_key, $this->inner_endpoint, $this->is_cname);
            }
            return self::$innerOss;
        }
    }

    public function buildMediumInfo(){
        return [
            'bucket;' => $this->bucket,
            'access_key_id;' => $this->access_key_id,
            'access_secret_key;' => $this->access_secret_key,
            'is_cname;' => $this->is_cname,
            'endpoint;' => $this->endpoint,
            'inner_endpoint;' => $this->inner_endpoint,
            'base;' => $this->base,
        ];
    }

}
