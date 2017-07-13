<?php
namespace common\models\file;

use Yii;
use common\models\Model;
use common\models\file\File;
use common\models\file\drivers\Disk;

/**
 *
 */
class FileModel extends Model
{

    /**
     * 创建一个文件对象
     * @param  array $data 文件对象的基础数据
     * - is_private: integer, 文件访问属性
     *   定义文件的访问属性，默认值是0
     *   1:私有文件
     *   0:共有文件
     * - is_tmp: integer, 文件是否是临时文件
     *   定义文件的时效型，默认值是0
     *   1:是临时文件，将被定时任务定时清理
     *   0:永久文件
     * - valid_time: integer, 临时文件的有效时间
     *   仅当is_tmp为1的时候有效，如定义3600,说明文件在服务器的有效时间是3600秒
     * - save_name: string, 文件的下载名称
     *   该属性将被用于设置文件下载时的响应头
     * - source_path: string, 文件的源路径
     *   注意文件路径必须携带完整的文件名称，包括文件后缀
     * - category: string, 文件分类信息
     *   文件分类如
     *   /user/image/ 将会被转成 user/image
     *   最中被计算为md5
     * @return [type]       [description]
     */
    public function createFile($data){
        $file = new File();
        if(!$file->load($data, '') || !$file->validate()){
            $this->addError('', $this->getOneErrMsg($file));
            return false;
        }
        $file->ext = pathinfo($file->save_name, PATHINFO_EXTENSION);
        $file->md5_value = md5_file($file->source_path);
        $file->prefix = md5($file->category);
        return $file;
    }

    public function saveFile(File $file){
        $file->save_path = self::buildFileSavePath($file);
        $saveMedium = $this->getSaveMedium($file->save_type);
        $file = $saveMedium->save($file);
        return $file;
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
        return $file->save_type . ':' .
               $file->category . '/' . basename($file->save_path);
    }
    public static function buildFileUrl(File $file){
        return self::getSaveMedium($file->save_type)->buildFileUrl($file);
    }
    protected static function buildFileSavePath(File $file){
        return             $file->prefix . '/' .
                           md5(
                             $file->md5_value .
                             microtime(true)
                           ) .
                           ($file->ext ? '.' . $file->ext : '')
                           ;
    }


    public function saveFileInDb(File $file){
        return $file;
    }


}
