<?php
namespace kshopapi\controllers;

use Yii;
use common\controllers\ApiController as Controller;
use common\models\file\FileModel;
use common\models\file\queryFileQuery;
use common\models\file\ar\FileTask;
use common\models\file\ar\File;
use common\helpers\ArrayHelper;
use common\models\file\drivers\Disk;
use common\models\file\drivers\Oss;
use yii\web\NotFoundHttpException;
use yii\base\InvalidParamException;
use yii\web\ForbiddenHttpException;
/**
 *
 */
class FileController extends Controller
{
    public function actionIndex(){

    }



    public function actionChunkTaskCreate(){
        $fileModel = new FileModel();
        $post = Yii::$app->request->getBodyParams();
        // todo 检查 access_token 的合法性,应该在数据库中检查
        if(empty($post['access_token'])){
            return $this->error('', Yii::t('app', 'access_token不合法'));
        }
        $fileTask = $fileModel->createFileChunkedUploadTask($post);
        if(!$fileTask){
            list($code, $message) = $fileModel->getOneError();
            return $this->error($code, $message);
        }
        return $this->succ($fileTask->toArray());
    }

    public function actionCreate(){
        $post = Yii::$app->request->getBodyParams();
        $fileModel = new FileModel();
        if(!empty($post['file_md5_value'])){
            // 从文件md5值在服务端进行拷贝
            $file = FileQuery::find()->where(['file_md5_value' => $post['file_md5_value']])->one();
            if(!$file){
                return $this->error(404, Yii::t('app', "{$post['file_md5_value']}相关文件不存在"));
            }
            $fileCopy = $fileModel->createFile(array_merge($file->toArray(), $post), true);
            if(!$fileCopy){
                list($code, $message) = $fileModel->getOneError();
                return $this->error($code, $message);
            }

            $fileCopy = $fileModel->saveFileByCopy($fileCopy, $file);
            if(!$fileCopy){
                list($code, $message) = $fileModel->getOneError();
                return $this->error($code, $message);
            }

            $fileCopy = $fileModel->saveFileInDb($fileCopy);
            if(!$fileCopy){
                list($code, $message) = $fileModel->getOneError();
                return $this->error($code, $message);
            }
            return $this->succ($fileCopy->toArray());

        }elseif(empty($post['chunks'])){
            // 从文件流来上传, 不分片
            if(empty($_FILES) || empty($_FILES['file']) || $_FILES["file"]["error"]){
                return $this->error(null, Yii::t('app','没有文件数据'));
            }
            $post['file_save_name'] = empty($post['file_save_name']) ? ($_FILES['file']['name']) : $post['file_save_name'];
            $fileData = array_merge([
                'file_source_path' => $_FILES['file']['tmp_name']
            ], $post);
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
        }else{
            // 文件流分片上传
            $post = Yii::$app->request->post();

            $fileResult = $fileModel->createFilePart($post, []);;
            if(!$fileResult){
                list($code, $error) = $fileModel->getOneError();
                return $this->error($code, $error);
            }
            if($fileResult instanceof File){
                return $this->succ($fileResult->toArray());
            }
            return $this->succ($fileResult);
        }
    }

    public function actionOutput($query_id){
        $get = Yii::$app->request->get();
        $fileInfo = FileModel::parseQueryId($query_id);

        $file = FileQuery::find()->where($fileInfo)->one();
        if(!$file){
            throw new NotFoundHttpException(Yii::t('app', "{$query_id} 文件不存在"));
        }
        if($file->file_is_private && (empty($get['signature']) || !FileModel::checkSignature($get['signature'], $get))){
            throw new ForbiddenHttpException(Yii::t('app', "您没有权限访问该文件"));
        }
        if(Disk::NAME == $file->file_save_type){
            return Yii::$app->response->sendFile($file->getFileDiskFullSavePath(), $file->file_save_name, ['inline' => true]);
        }elseif(Oss::NAME == $file->file_save_type){
            $url = $file->file_url;
            header("location:{$url}");
        }else{
            throw new InvalidParamException(Yii::t('app', "不支持的输出类型" . $file->file_save_type));
        }
    }
}
