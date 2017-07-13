<?php
namespace common\models\file;

use common\models\Model;
use common\models\file\File;

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
     * -
     * @return [type]       [description]
     */
    public function createFile($data){
        $file = new File();
        if(!$file->load($data, '') || !$file->validate()){
            $this->addErrors($file->getErrors());
            return false;
        }
        return $file;
    }

    public function saveFile(File $file){
        return $file;
    }

    public function saveFileInDb(File $file){
        return $file;
    }

    /**
     * 从$_FILES中的数据构建文件的保存名称
     * @param  [type] $tmpFileInfo [description]
     * @param  string $saveName    [description]
     * @return [type]              [description]
     */
    public static function buildFileSafeName($tmpFileInfo, $saveName = ''){
        $saveName = empty($saveName) ? $tmpFileInfo['name'] : $saveName;
        $fileExt = pathinfo($saveName, PATHINFO_EXTENSION);
        $safeName = preg_replace('/[^\w_-]+/u', '', pathinfo($saveName, PATHINFO_FILENAME));
        return $fileExt ? $safeName . '.' . $fileExt : $safeName;
    }
}
