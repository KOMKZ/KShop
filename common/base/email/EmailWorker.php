<?php
namespace common\models\email;

use Yii;
use common\models\email\Mail;
use common\models\email\EmailModel;

class EmailWorker
{
    CONST SEN_FIELD = 'worker:email:succ_email_num';

    static public $emailWorkerCount = 5;



    private static $redis;
    private static $db;



    public static function handleEmail($msg){
        $data = json_decode($msg->body, true);
        if(false == $data){
            return false;
        }
        $data['class'] = Mail::className();
        $mail = Yii::createObject($data);
        if(!$mail->check()){
            EmailModel::insertFailedEmail($msg->body, 0, $mail->getErrors());
            return $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        }
        if(!$mail->send()){
            EmailModel::insertFailedEmail($msg->body, 0, $mail->getErrors());
            return $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        }
        // 4. 计数等成功处理
        Yii::$app->redis->executeCommand('incr', [self::SEN_FIELD]);
        // 5. confirm ack
        return $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }


}
