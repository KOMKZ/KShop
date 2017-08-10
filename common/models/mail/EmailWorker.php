<?php
namespace common\models\mail;

use Yii;
use common\models\mail\Mail;
use common\models\mail\MailModel;
use common\models\set\SetModel;

class EmailWorker
{
    public static function handleEmail($msg){
        $mailData = json_decode($msg->body, true);
        if(false == $mailData){
            Yii::error([$msg, "fail json_decode"]);
            return false;
        }
        $beginTime = microtime(true);
        list($result, $sendResult) = MailModel::sendMail($mailData);
        $consume = sprintf('%.3f', microtime(true) - $beginTime);
        list(, $settingName) = explode(':', $mailData['mail_meta_data']['sender_info']);
        $senderInfo = SetModel::get($settingName);
        MailModel::saveSendResult([
            'mail_id' => $mailData['mail_id'],
            'mail_sender' => $senderInfo['sender'],
            'mail_receipt' => $mailData['mail_meta_data']['address'],
            'mail_status' => $sendResult['status'],
            'mail_error' => $sendResult['error'],
            'mail_send_at' => time(),
            'mail_updated_at' => time(),
            'mail_consume' => (float)$consume
        ]);
        echo implode(',', $sendResult) . "\n";
        return $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }


}
