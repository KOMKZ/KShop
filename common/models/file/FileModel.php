<?php
namespace common\models\file;

use Yii;
use common\models\Model;
use common\models\file\ar\File;
use common\models\file\ar\FileTask;
use common\models\file\drivers\Disk;
use common\models\file\drivers\Oss;
use common\models\staticdata\ConstMap;
use yii\helpers\FileHelper;
use common\helpers\ArrayHelper;
use yii\base\InvalidParamException;
use yii\base\InvalidArgumentException;
use common\models\file\FileTaskQuery;

/**
 *
 */
class FileModel extends Model
{

    public function saveFileByCopy(File $targetFile, File $originFile){
        $saveMedium = $this->getSaveMedium($originFile->file_save_type);
        if($targetFile->file_save_type == $originFile->file_save_type){
            $file = $saveMedium->saveByCopy($targetFile, $originFile);
        }else{
            throw new \Exception("还没有实现");
        }
        $file->file_medium_info = json_encode($saveMedium->buildMediumInfo());
        $file->file_created_time = time();
        return $file;
    }

    /**
     * 创建一个文件对象
     * @param  array $data 文件对象的基础数据
     * - file_is_private: integer, 文件访问属性
     *   定义文件的访问属性，默认值是0
     *   1:私有文件
     *   0:共有文件
     * - file_is_tmp: integer, 文件是否是临时文件
     *   定义文件的时效型，默认值是0
     *   1:是临时文件，将被定时任务定时清理
     *   0:永久文件
     * - file_valid_time: integer, 临时文件的有效时间
     *   仅当is_tmp为1的时候有效，如定义3600,说明文件在服务器的有效时间是3600秒
     * - file_save_name: string, 文件的下载名称
     *   该属性将被用于设置文件下载时的响应头
     * - file_source_path: string, 文件的源路径
     *   注意文件路径必须携带完整的文件名称，包括文件后缀
     * - file_category: string, 文件分类信息
     *   文件分类如
     *   /user/image/ 将会被转成 user/image
     *   最中被计算为md5
     * @return [type]       [description]
     */
    public function createFile($data, $isCopy = false){
        if(!$file = $this->validateFileData($data)){
            return false;
        }
        $file->file_ext = pathinfo($file->file_save_name, PATHINFO_EXTENSION);
        $file->file_md5_value = $isCopy ? $data['file_md5_value'] : md5_file($file->file_source_path);
        $file->file_prefix = self::buildPrefix($file->file_category);
        $file->file_real_name = self::buildFileRealName($file);
        $file->file_valid_time = 1 == $file->file_is_tmp ? $file->file_valid_time : 0;
        return $file;
    }

    public function createFilePart($fileInfo, $fileStream = null){
        if(!isset($fileInfo['chunk']) || !isset($fileInfo['chunks']) || !is_numeric($fileInfo['chunk']) || !is_numeric($fileInfo['chunks'])){
            $this->addError("", Yii::t('app', '分片上传参数不完整'));
            return false;
        }
        $fileTask = FileTaskQuery::findOneCUByData($fileInfo);
        if(!$fileTask || !static::checkFileTaskIsExpired($fileTask)){
            $this->addError('', Yii::t('app', "分片任务不存在/文件任务已经失效"));
            return false;
        }
        $chunkIndex = (int)$fileInfo['chunk'];
        $chunkTotal = (int)$fileInfo['chunks'];
        if(0 == $chunkIndex){
            // 先验证文件信息
            if(!$this->validateFileData($fileInfo, 'chunkupload')){
                return $this->error($code, $message);
            }
            // 初始化分片目录
            $chunkDir = static::buildFileChunkDir($fileTask);
            $fileInfoFile = $chunkDir . '/file.txt';
            file_put_contents($fileInfoFile, serialize($fileInfo));
        }
        $chunkDir = static::getFileChunkDir($fileTask);
        if(empty($_FILES) || empty($_FILES['file']) || $_FILES["file"]["error"]){
            $this->addError('', Yii::t('app','没有文件数据/文件上传错误:'. $_FILES['file']['error']));
            return false;
        }
        $chunkFile = $chunkDir . '/file.' . $chunkIndex;
        $new = 0;
        if(!file_exists($chunkFile) || $_FILES['file']['size'] != filesize($chunkFile)){
            move_uploaded_file($_FILES['file']['tmp_name'], $chunkFile);
            $new = 1;
        }
        if($chunkIndex != $chunkTotal - 1){
            return ['chunk' => $chunkIndex, 'new' => $new];
        }
        $finalFilePath = $chunkDir . '/file.final';
        if(file_exists($finalFilePath)){
            unlink($finalFilePath);
        }
        $finalFile = @fopen($chunkDir . '/file.final', "ab");
        if(!$finalFile){
            $this->addError('', Yii::t('app', '打开文件流失败'));
            return false;
        }
        $i = 0;
        while($i < $chunkTotal){
            $in = @fopen($chunkDir . '/file.' . $i, "rb");
            if(!$in){
                return $this->error('', Yii::t('app', '打开分片文件流失败'));
                return false;
            }
            while ($buff = fread($in, 4096))fwrite($finalFile, $buff);
            @fclose($in);
            $i++;
        }
        @fclose($finalFile);
        $fileInfo = unserialize(file_get_contents($chunkDir . '/file.txt'));
        $fileData = array_merge([
            'file_source_path' => $finalFilePath
        ], $fileInfo);
        $file = $this->createFile($fileData);
        if(!$file){
            return false;
        }
        $file = $this->saveFile($file);
        if(!$file){
            return false;
        }
        $file = $this->saveFileInDb($file);
        if(!$file){
            return false;
        }
        FileHelper::removeDirectory($chunkDir);
        return $file;
    }

    public function validateFileData($data, $scenario = 'default'){
        $file = new File();
        $file->scenario = $scenario;
        if(!$file->load($data, '') || !$file->validate()){
            $this->addError('', $this->getOneErrMsg($file));
            return false;
        }
        return $file;
    }

    public function createFileTask($data){
        $fileTask = new FileTask();
        if(!$fileTask->load($data, '') || !$fileTask->validate()){
            $this->addError('', $this->getOneErrMsg($fileTask));
        }
        if(!$fileTask->insert(false)){
            $this->addError('', Yii::t('app', '数据库插入失败'));
            return false;
        }
        return $fileTask;
    }

    public function createFileChunkedUploadTask($fileInfo = [], $validDuration = 86400){
        $fileTaskData = [
            'file_task_code' => static::buildTaskUniqueString('hash_post', $fileInfo),
            'file_task_invalid_at' => time() + $validDuration,
            'file_task_type' => FileTask::TASK_CHUNK_UPLOAD,
            'file_task_data' => json_encode($fileInfo),
        ];
        return $this->createFileTask($fileTaskData);
    }

    public static function buildTaskUniqueString($type = 'hash_post', $data = []){
        switch ($type) {
            case 'hash_post':
                $hashAttrs = array_merge(['access_token', 'timestamp'], (new File())->attributes());
                foreach($data as $key => $value){
                    if(!in_array($key, $hashAttrs)) unset($data[$key]);
                }
                return empty($data) ? null : md5(ArrayHelper::concatAsString($data));
            case 'simple_uniqid':
                return md5(microtime(true) . uniqid());
            default:
                throw new InvalidArgumentException(Yii::t("{$type}不支持的参数值"));
                break;
        }
    }

    public static function clearInvalidTask(){
        return FileTask::deleteAll("
            file_task_invalid_at <= :current_time
            or
            file_task_status = :status
        ", [
            ':current_time' => time(),
            ':status' => FileTask::STATUS_INVALID
        ]);
    }

    public function saveFile(File $file){
        $saveMedium = $this->getSaveMedium($file->file_save_type);
        try {
            $file = $saveMedium->save($file);
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError('', $e->getMessage());
            return false;
        }

        $file->file_medium_info = json_encode($saveMedium->buildMediumInfo());
        $file->file_created_time = time();
        return $file;
    }

    public function saveFileInDb(File $file){
        if(!$file->insert(false)){
            $this->addError('', Yii::t('app', '数据库插入失败'));
            return false;
        }
        return $file;
    }

    public static function checkSignature($signature, $data = []){
        return false;
    }

    public static function checkFileTaskIsExpired(FileTask $fileTask){
        return true;
        return time() < $fileTask->file_task_invalid_at;
    }

    public static function parseQueryId($string){
        $typeList = implode('|', ConstMap::getConst('file_save_type', true));
        if(preg_match("/^({$typeList}):{1}(.+)/", $string, $matches)){
            $fileCondition = [];
            $fileCondition['file_save_type'] = $matches[1];
            $fileCondition['file_prefix'] = self::buildPrefix(trim(dirname($matches[2]), '/'));
            $fileCondition['file_real_name'] = basename(trim($matches[2]));
            return $fileCondition;
        }else{
            return [];
        }
    }
    public static function getFileChunkDir(FileTask $fileTask){
        return Yii::getAlias('@app/runtime/file_chunk/') . $fileTask->file_task_code;
    }
    public static function getSaveMedium($type){
        switch ($type) {
            case Disk::NAME:
                return Yii::$app->filedisk;
            case Oss::NAME:
                return Yii::$app->fileoss;
            default:
                throw new InvalidParamException(Yii::t('app', "{$type} 不支持的存储类型"));
                break;
        }
    }
    public static function buildFileChunkDir(FileTask $fileTask){
        $baseDir = dirname(self::getFileChunkDir($fileTask));
        if(!is_dir($baseDir)){
            FileHelper::createDirectory($baseDir);
            //todo fix
            chmod($baseDir, 0777);
        }
        $chunkDir = self::getFileChunkDir($fileTask);
        if(!is_dir($chunkDir)){
            FileHelper::createDirectory($chunkDir);
            // todo fix
            chmod($chunkDir, 0777);
        }
        return $chunkDir;
    }
    public static function buildPrefix($value){
        return md5($value);
    }
    public static function buildFileQueryId(File $file){
        return $file->file_save_type . ':' .
               $file->file_category . '/' . $file->file_real_name;
    }
    public static function buildFileUrl(File $file){
        return self::getSaveMedium($file->file_save_type)->buildFileUrl($file);
    }

    public static function buildFileSavePath(File $file){
        return $file->file_prefix . '/' . $file->file_real_name;
    }

    protected static function buildFileRealName(File $file){
        return             $file->file_real_name ? $file->file_real_name : md5(
                             $file->file_md5_value .
                             microtime(true)
                           ) .
                           ($file->file_ext ? '.' . $file->file_ext : '')
                           ;
    }





}
