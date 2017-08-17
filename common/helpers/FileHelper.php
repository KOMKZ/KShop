<?php
namespace common\helpers;

use Yii;
use yii\helpers\BaseFileHelper;

/**
 *
 */
class FileHelper extends BaseFileHelper
{
    public static function buildFileSafeName($saveName){
        $fileExt = preg_replace('/[^\w_-]+/u', '', pathinfo($saveName, PATHINFO_EXTENSION));
        $safeName = preg_replace('/[^\w_-]+/u', '', pathinfo($saveName, PATHINFO_FILENAME));
        return $fileExt ? $safeName . '.' . $fileExt : $safeName;
    }
    public static function unzipFileToDir($zipFile, $sourceEncode = ''){
        $runTimeDir = Yii::getAlias('@app/runtime/unzip');
        if(!is_dir($runTimeDir)){
            mkdir($runTimeDir);
        }
        $targetBasePath = $runTimeDir . '/' . md5_file($zipFile);
        if(!is_dir($targetBasePath)){
            mkdir($targetBasePath);
        }
        $detectEncode = 'UTF-8, GB2312, BIG-5, SHIFT-JIS, ISO-8859-1, GB18030, GBK';
        $file = zip_open($zipFile);
        if(!is_resource($file)){
            throw new \Exception(Yii::t('app', "Open zip file not ok. " . $zipFile));
		}
        while($entry = zip_read($file)){
			$fileSize = zip_entry_filesize($entry);
			$fileName = zip_entry_name($entry);
            $encode = $sourceEncode ? $sourceEncode : mb_detect_encoding($fileName, $detectEncode);
			if(strtoupper(trim($encode)) !== 'UTF-8'){
				$fileName = iconv($encode, 'UTF-8//IGNORE', $fileName);
			}
			$targetFilePath = $targetBasePath . '/' . $fileName;
			if(!$fileSize){
				if(file_exists($targetFilePath)){
					continue;
				}
				continue;
			}elseif(!zip_entry_open($file, $entry)){
				continue;
			}
			file_put_contents($targetFilePath, iconv($encode, 'utf-8', zip_entry_read($entry, $fileSize)));
			zip_entry_close($entry);
		}
        return $targetBasePath;
    }
}
