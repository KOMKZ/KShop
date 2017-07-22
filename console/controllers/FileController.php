<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use common\models\file\FileModel;

class FileController extends Controller{
    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            []
        );
    }
    public function actionClearInvalidTask(){
        $affects = FileModel::clearInvalidTask();
        echo sprintf("%s:%s\n", Yii::t('app', '清理无效的文件任务:'), $affects);
    }
}
