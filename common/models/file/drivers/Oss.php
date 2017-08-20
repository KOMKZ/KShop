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

    /**
     * oss存储的名称
     * @var [type]
     */
    CONST NAME = 'oss';

    /**
     * oss存储的bucket
     * @var string
     */
    public $bucket;

    /**
     * oss存储访问key
     * @var string
     */
    public $access_key_id;

    /**
     * oss存储访问密钥
     * @var string
     */
    public $access_secret_key;

    /**
     * endpoint是否时别名，即是不是域名
     * @var string
     */
    protected $is_cname;

    /**
     * Oss存储节点
     * @var string
     */
    public $endpoint;

    /**
     * oss存储内网节点
     * @var string
     */
    public $inner_endpoint;

    /**
     * oss存储bucket的根目录
     * @var string
     */
    protected $base;

    /**
     * 私有文件url的默认有效时间
     * @var [type]
     */
    public $timeout = 3600;

    /**
     * oss客户端实例
     * @var 、OSS\OssClient
     */
    protected static $oss;

    /**
     * oss内网客户端实例
     * @var \OSS\OssClient
     */
    protected static $innerOss;

    /**
     * 设置文件存储的根目录
     * @param string $value 更目录
     */
    public function setBase($value){
        $this->base = trim($value, '/');
    }

    /**
     * 是否是别名设置
     * @param integer|bool $value 是否是别名设置
     */
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

    /**
     * 构建文件访问url
     * @param  File   $file   统一文件对象
     * @param  array  $params 额外参数
     * @return string         文件访问url
     * 返回的url可能带签名也有不带签名，主要有file_is_private决定
     */
    public function buildFileUrl(File $file, $params = []){
        if(!$file->file_is_private){
            $host = $this->getHostName();
            $objectName = $this->buildFileObjectName($file);
            return "http://" . $host . '/' . $objectName;
        }else{
            $objectName = $this->buildFileObjectName($file);
            $originMedium = json_decode($file->file_medium_info);
            return $this->createClient()->signUrl($originMedium->bucket, $objectName, $this->timeout , OssClient::OSS_HTTP_GET, []);
        }
    }

    /**
     * 获取oss节点的域名
     * 该方法将返回内网域名和外网域名
     * @param  boolean $inner 是否是内网域名
     * @return string         内网域名
     */
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

    /**
     * 构建文件的保存名称
     * 本地存储中叫做路径，oss中叫做对象名称
     * @param  File   $file 统一文件对象
     * @return string       oss对象名称
     */
    public function buildFileObjectName(File $file){
         return $this->base . '/' . $file->getFileSavePath();
    }

    /**
     * 通过在oss端直接复制对象来实现上传
     * @param  File   $targetFile 目标文件对象
     * @param  File   $originFile 原始文件对象
     * @return File             目标文件对象
     */
    public function saveByCopy(File $targetFile, File $originFile){
        $client = $this->createClient(true);
        $originObjectName = $this->buildFileObjectName($originFile);
        $targetObjectName = $this->buildFileObjectName($targetFile);
        $originMedium = json_decode($originFile->file_medium_info);
        $options[OssClient::OSS_HEADERS] = [
            'Content-Disposition' => sprintf('%s; filename="%s"', 'inline', $targetFile->file_save_name)
        ];
        $client->copyObject($originMedium->bucket, $originObjectName, $this->bucket, $targetObjectName, $options);
        if(!$targetFile->file_is_private){
            $client->putObjectAcl($this->bucket, $targetObjectName, OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE);
        }

        return $targetFile;
    }

    /**
     * 设置文件的下载名称和窗口的打开方式
     * 和本地存储不同，输出文件流的控制在oss端，故需要预先设置文件的Content-Disposition头来设置下载名称
     * @param File    $file   统一文件对象
     * @param string  $name   保存名称，不要带文件后缀
     * @param boolean $inline 不打开新的窗口来下载
     */
    public function setFileDownloadName(File $file, $name = '', $inline = true){
        $options = [];
        $options[OssClient::OSS_HEADERS] = [
            'Content-Disposition' => sprintf('%s; filename="%s"', $inline ? 'inline' : 'attachment', $name ? $name : $file->file_save_name)
        ];
        $originMedium = json_decode($file->file_medium_info);
        $objectName = $this->buildFileObjectName($file);
        $this->createClient(true)->copyObject($originMedium->bucket, $objectName, $originMedium->bucket, $objectName, $options);
    }

    /**
     * 上传文件对象到Oss中
     * 注意文件对象最好是通过\common\models\file\FileModel::createFile创建得到
     * 注意这个方法会抛异常，需要上层调用处理异常
     * @param  File   $file 统一文件对象
     * @return File       统一文件对象
     */
    public function save(File $file){
        $client = $this->createClient(true);
        $objectName = $this->buildFileObjectName($file);
        $options = [];
        $options[OssClient::OSS_CONTENT_LENGTH] = filesize($file->file_source_path);
        $options[OssClient::OSS_HEADERS] = [
            'Content-Disposition' => sprintf('%s; filename="%s"', 'inline', $file->file_save_name)
        ];
        $client->uploadFile($this->bucket, $objectName, $file->file_source_path, $options);
        if(!$file->file_is_private){
            $client->putObjectAcl($this->bucket, $objectName, OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE);
        }
        return $file;
    }

    /**
     * 创建oss实例
     * @param  boolean $inner 是否创建为内网实例
     * @return \OSS\OssClient   oss实例
     */
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

    /**
     * 构造oss存储元信息
     * @return array 
     */
    public function buildMediumInfo(){
        return [
            'bucket' => $this->bucket,
            'access_key_id' => $this->access_key_id,
            'access_secret_key' => $this->access_secret_key,
            'is_cname' => $this->is_cname,
            'endpoint' => $this->endpoint,
            'inner_endpoint' => $this->inner_endpoint,
            'base' => $this->base,
        ];
    }

}
