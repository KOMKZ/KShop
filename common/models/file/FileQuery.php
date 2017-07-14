<?php
namespace common\models\file;

use yii\base\Object;
use common\models\file\ar\File;

/**
 *
 */
class FileQuery extends Object{
    public static function find(){
        return File::find();
    }
}
