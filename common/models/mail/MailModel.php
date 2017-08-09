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
            if($now){
                list($result, $sendResult) = static::sendMail($mailData);
                console($result, $sendResult);
            }else{

            }
        }
        console($mailData);
    }
    public static function sendMail($mailData){
        $sendResult = [
            'status' => 0,
            'error' => '',
        ];
        $result = [];
        try {
            // todo check metainfo
            list(, $settingName) = explode(':', $mailData['mail_meta_data']['sender_info']);
            $senderInfo = SetModel::get($settingName);
            $mail = new \PHPMailer;
            $mail->isSMTP();
            $mail->Host = $senderInfo['host'];
            $mail->SMTPAuth = (boolean)$senderInfo['smtp_auth'];
            $mail->Username = $senderInfo['sender'];
            $mail->Password = $senderInfo['sender_pwd'];
            $mail->SMTPSecure = $senderInfo['smtp_secure'];
            $mail->Port = $senderInfo['port'];

            $mail->setFrom($senderInfo['sender']);
            $mail->addAddress('784248377@qq.com');
            $mail->addAddress('m13715194169_1@163.com');
            $mail->Subject = "测试邮件";
            $mail->CharSet = $senderInfo['connect_charset'];
            switch ($mailData['mail_content_type']) {
                case 'text/html':
                    $mail->isHTML(true);
                    break;
                default:
                    throw new InvalidParamException(Yii::t('app', "{$mailData['mail_content_type']}不合法"));
                    break;
            }
            $mail->addAddress($mailData['mail_meta_data']['address']);
            $mail->Subject = $mailData['mail_title'];
            $mail->Body = empty($mailData['mail_type']) ? $mailData['mail_content'] :
                          static::renderEmailBodyByTpl($mailData['mail_type'], ArrayHelper::getValue($mailData, 'mail_meta_data.content_params'));
            foreach($mailData['mail_attachments'] as $fileItem){
                if(file_exists($fileItem['file'])){
                    $mail->addAttachment($fileItem['file'], $fileItem['name']);
                }
            }
            if(!$mail->send()){
                $sendResult['error'] = $mail->ErrorInfo;
                $sendResult['status'] = Errno::EMAIL_SEND_FAIL;
            }
        } catch (\Exception $e) {
            Yii::error($e);
            $sendResult['error'] = $e->getMessage();
            $sendResult['status'] = Errno::EXCEPTION;
        }

        return [$result, $sendResult];
    }
    public static function sendMailToQueue($mailData){

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
            }else{
                $attachments[$key]['name'] = basename($item['file']);
            }
        }
        return $attachments;
    }





}
