<?php
namespace common\models\file;

use yii\base\Object;
use common\models\file\ar\FileTask;

/**
 *
 */
class FileTaskQuery extends Object{
    public static function find(){
        return FileTask::find();
    }
}
