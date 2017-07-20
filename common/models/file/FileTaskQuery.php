<?php
namespace common\models\file;

use yii\base\Object;
use common\models\file\ar\FileTask;
use common\models\file\FileModel;

/**
 *
 */
class FileTaskQuery extends Object{
    public static function find(){
        return FileTask::find();
    }
    public static function findOneCUByData($data, $type = 'hash_post'){
        return FileTaskQuery::find()->
                              where(['file_task_code' => FileModel::buildTaskUniqueString($type, $data), 'file_task_type' => FileTask::TASK_CHUNK_UPLOAD])->
                              one();
    }
}
