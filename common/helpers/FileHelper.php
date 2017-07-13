<?php
namespace common\helpers;

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
}
