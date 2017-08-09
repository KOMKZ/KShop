<?php
namespace common\models\mail;

use Yii;
use common\models\Model;
use common\models\mail\ar\Mail;
use common\models\staticdata\Errno;
use common\models\set\SetModel;
use yii\helpers\ArrayHelper;
use common\models\file\FileModel;
use common\models\file\query\FileQuery;
use yii\base\InvalidParamException;
/**
 *
 */
class MailModel extends Model
{
    public function createMail($data){
        $mail = new Mail();

        if(!$mail->load($data, '') || !$mail->validate()){
            $this->addError("", $this->getOneErrmsg($mail));
            return false;
        }
        $mail->mail_created_at = time();

        if(!$mail->insert(false)){
            $this->addError(Errno::DB_FAIL_INSERT, "创建邮件记录失败");
            return false;
        }
        return $mail;
    }
    public function send($mail, $now = false){
        $metaData = $mail->meta_data;
        $addresses = $this->parseAddressList($metaData['addresses']);
        $mailData = $mail->toArray();
        unset($mailData['mail_meta_data']['addresses']);
        foreach($addresses as $address){
            $mailData['mail_meta_data']['address'] = $address;
        }
        console($mailData);
    }
    protected function parseAddressList($data){
        switch ($data['type']) {
            case Mail::LIST_TYPE_INLINE:
                return $data['list'];
                break;
            default:
                throw new InvalidParamException(Yii::t('app', "不支持的mail_list_type类型:{$data['type']}"));
                break;
        }
    }
    public static function buildRealMail(){

    }

    public static function filterMetaData($metaData){
        $data = [
            'sender_info' => [],
            'addresses' => []
        ];
        $metaData = ArrayHelper::merge($data, $metaData);
        if(empty($metaData['sender_info'])){
            $metaData['sender_info'] = 'setting:email.default_sender';
        }
        return $metaData;
    }

    public static function filterAttachments($attachments){
        foreach($attachments as $key => $item){
            if(FileModel::checkIsValidQueryid($item['file'])){
                $fileInfo = FileModel::parseQueryId($item['file']);
                $file = FileQuery::find()->where($fileInfo)->one();
                if(!$file)continue;
                // $file->getFileDiskFullSavePath(), $file->file_save_name, ['inline' => true]
                $attachments[$key]['file'] = $file->getFileDiskFullSavePath();
                $attachments[$key]['name'] = $file->file_save_name;
            }
        }
        return $attachments;
    }

    public static function filterCronParams($params){
        return $params;
    }




}
