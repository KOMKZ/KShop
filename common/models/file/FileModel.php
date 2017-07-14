<?php
namespace common\models\file;

use Yii;
use common\models\Model;
use common\models\file\ar\File;
use common\models\file\drivers\Disk;
use common\models\staticdata\ConstMap;

/**
 *
 */
class FileModel extends Model
{

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
    public function createFile($data, $safe = false){
        $file = new File();
        if(!$file->load($data, '') || !$file->validate()){
            $this->addError('', $this->getOneErrMsg($file));
            return false;
        }
        $file->file_ext = pathinfo($file->file_save_name, PATHINFO_EXTENSION);
        $file->file_md5_value = md5_file($file->file_source_path);
        $file->file_prefix = md5($file->file_category);
        $file->file_real_name = self::buildFileRealName($file);
        $file->file_valid_time = 1 == $file->file_is_tmp ? $file->file_valid_time : 0;
        return $file;
    }

    public function saveFile(File $file){
        $saveMedium = $this->getSaveMedium($file->file_save_type);
        $file = $saveMedium->save($file);
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


    public static function parseQueryId($string){
        $typeList = implode('|', ConstMap::getConst('file_save_type', true));
        if(preg_match("/^({$typeList}):{1}(.+)/", $string, $matches)){
            $fileCondition = [];
            $fileCondition['file_save_type'] = $matches[1];
            $fileCondition['file_prefix'] = trim(dirname($matches[2]), '/');
            $fileCondition['file_real_name'] = trim($matches[2]);
            return $fileCondition;
        }else{
            return [];
        }
    }

    public static function getSaveMedium($type){
        switch ($type) {
            case Disk::NAME:
                return Yii::$app->filedisk;
                break;
            default:
                throw new \InvalidParamException(Yii::t('app', "{$type} 不支持的存储类型"));
                break;
        }
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
