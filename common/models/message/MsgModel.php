<?php
namespace common\models\message;

use common\models\Model;
use common\models\message\ar\MessageMap;
use common\models\user\ar\UserMessage;
use common\models\message\Message;

/**
 *
 */
class MsgModel extends Model
{
    public function send(Message $msg){
        if(!$msg->validate()){
            $this->addError('', $this->getOneErrMsg($msg));
            return false;
        }
        if(Message::TYPE_ONE == $msg->type){
            $data = [
                'u_id' => $msg->receipt_uid,
                'um_from_uid' => $msg->create_uid,
                'um_type' => $msg->type,
                'um_content_type' => $msg->content_type,
                'um_content' => $msg->getFinalContent(),
            ];
            $userMsg = $this->createUnreadUsrMsg($data);
            if(!$userMsg){
                return false;
            }
            return true;
        }elseif(Message::TYPE_BOARD == $msg->type){

        }else{
            throw new \Exception(Yii::t('app', "{$msg->type}不支持的类型"));
        }
    }

    public static function buildFinalContent(Message $msg){
        $render = [static::className()];
        switch ($msg->content_type) {
            case Message::CONTENT_TYPE_PLAIN:
                array_push($render, 'renderPlainMsg');
                break;
            case Message::CONTENT_TYPE_TEMPLATE:
                array_push($render, 'renderTplMsg');
                break;
            default:
                throw new \Exception(Yii::t('app', "不支持的content_type:{$msg->content_type}"));
                break;
        }
        return call_user_func_array($render, [$msg]);
    }

    protected static function renderHtmlMsg(Message $msg){

    }

    protected static function renderTplMsg(Message $msg){
        console($msg);
    }

    protected static function renderPlainMsg(Message $msg){
        return $msg->content;
    }





    protected function createUnreadUsrMsg($data){
        $userMsg = new UserMessage();
        if(!$userMsg->load($data, '') || !$userMsg->validate()){
            $this->addError('', $this->getOneErrMsg($userMsg));
            return false;
        }
        $userMsg->um_created_at = time();
        if(!$userMsg->insert(false)){
            $this->addError(Errno::DB_INSERT_FAIL, Yii::t('app', "插入用户未读消息失败"));
            return false;
        }
        return $userMsg;
    }

    public function createMessage($data){
        $message = new Message();
        if(!$message->load($data, '') || !$message->validate()){
            $this->addError('', $this->getOneErrMsg($message));
            return false;
        }
        return $message;
    }
}
