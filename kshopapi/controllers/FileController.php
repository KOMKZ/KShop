<?php
namespace kshopapi\controllers;

use Yii;
use common\controllers\ApiController as Controller;
use common\models\file\FileModel;
use common\models\file\FileQuery;
use yii\helpers\ArrayHelper;
use common\models\file\drivers\Disk;
use yii\web\NotFoundHttpException;
use yii\base\InvalidParamException;
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
        $post['file_save_name'] = empty($post['file_save_name']) ? ($_FILES['file']['name']) : $post['file_save_name'];
        $fileData = array_merge([
            'file_source_path' => $_FILES['file']['tmp_name']
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
        $file = $fileModel->saveFileInDb($file);
        if(!$file){
            list($code, $message) = $fileModel->getOneError();
            return $this->error($code, $message);
        }
        return $this->succ($file->toArray());
    }
    public function actionOutput($query_id){
        $fileInfo = FileModel::parseQueryId($query_id);
        if(Disk::NAME != $fileInfo['file_save_type']){
            throw new InvalidParamException(Yii::t('app', "只支持disk类型的文件"));
        }
        console($fileInfo);
        $file = FileQuery::find()->where($fileInfo)->one();
        if(!$file){
            throw new NotFoundHttpException(Yii::t('app', "{$query_id} 文件不存在"));
        }
        console($file->toArray());
    }
}
