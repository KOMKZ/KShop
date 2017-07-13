<?php
namespace kshopapi\controllers;

use Yii;
use common\controllers\ApiController as Controller;
use common\models\file\FileModel;
use yii\helpers\ArrayHelper;

/**
 *
 */
class FileController extends Controller
{
    public function actionIndex(){

    }
    public function actionCreate(){
        if(empty($_FILES) || empty($_FILES['file'])){
            return $this->error(null, Yii::t('app','没有文件数据'));
        }
        $post = Yii::$app->request->getBodyParams();
        $post['save_name'] = empty($post['save_name']) ? ($_FILES['file']['name']) : $post['save_name'];
        $fileData = array_merge([
            'source_path' => $_FILES['file']['tmp_name']
        ], $post);
        $fileModel = new FileModel();
        $file = $fileModel->createFile($fileData);
        if(!$file){
            list($code, $message) = $fileModel->getOneError();
            return $this->error($code, $message);
        }
        $file = $fileModel->saveFile($file);
        if(!$file){
            list($code, $message) = $fileModel->getOneError();
            return $this->error($code, $message);
        }
        console($file->toArray());
        $file = $fileModel->saveFileInDb($file);
        if(!$file){

        }

        return $this->succ($file->toArray());
    }
}
