<?php
namespace kshopapi\controllers;

use Yii;
use common\controllers\ApiController as Controller;
use common\models\file\FileModel;
use common\models\file\FileQuery;
use common\models\file\FileTaskQuery;
use common\models\file\ar\FileTask;
use yii\helpers\ArrayHelper;
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
        $fileInfo = [];
        $fileTask = $fileModel->createFileChunkedUploadTask($fileInfo);
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

        }elseif(empty($post['file_task_code'])){
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
            // chunk, chunks参数必须存在且类型正确
            if(!isset($post['chunk']) || !isset($post['chunks']) || !is_numeric($post['chunk']) || !is_numeric($post['chunks'])){
                return $this->error("", Yii::t('app', '分片上传参数不完整'));
            }
            // todo 考虑文件保存名称是否必须提供
            // todo sql太松散
            $fileTask = FileTaskQuery::find()->
                                       where(['file_task_code' => $post['file_task_code'], 'file_task_type' => FileTask::TASK_CHUNK_UPLOAD])->
                                       one();
            if(!$fileTask || !$fileModel::checkFileTask($fileTask)){
                return $this->error('', Yii::t('app', "{$post['file_task_code']}分片任务不存在/文件任务已经失效"));
            }
            $chunkIndex = (int)$post['chunk'];
            $chunkTotal = (int)$post['chunks'];
            if(0 == $chunkIndex){
                // 先验证文件信息
                if(!$fileModel->validateFileData($post, 'chunkupload')){
                    list($code, $message) = $fileModel->getOneError();
                    return $this->error($code, $message);
                }
                // 初始化分片目录
                $chunkDir = FileModel::buildFileChunkDir($fileTask);
                $fileInfoFile = $chunkDir . '/file.txt';
                file_put_contents($fileInfoFile, serialize($post));
            }

            console(1);
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
