<?php
namespace common\models\message;

use Yii;
use common\models\Model;
use common\models\message\ar\MessageMap;
use common\models\user\ar\UserMessage;
use common\models\user\ar\User;
use common\models\message\query\MsgQuery;
use common\models\message\Message;
use common\models\message\ar\MessageTpl;
use common\models\message\query\MessageTplQuery;
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
            $data = [
                'mm_create_uid' => $msg->create_uid
                ,'mm_type' => $msg->type
                ,'mm_content_type' => $msg->content_type
                ,'mm_tpl_code' => $msg->tpl_code
                ,'mm_content' => $msg->getBoardContent()
                ,'mm_vars' => $msg->tpl_params_string
            ];
            $boardMsg = $this->createUnreadBoardMsg($data);
            if(!$boardMsg){
                return false;
            }
            return true;
        }else{
            throw new \Exception(Yii::t('app', "{$msg->type}不支持的类型"));
        }
    }

    public static function buildFinalContent($data){
        $render = [static::className()];
        switch ($data['content_type']) {
            case Message::CONTENT_TYPE_PLAIN:
                array_push($render, 'renderPlainMsg');
                break;
            case Message::CONTENT_TYPE_TEMPLATE:
                array_push($render, 'renderTplMsg');
                break;
            default:
                throw new \Exception(Yii::t('app', "不支持的content_type:{$data['content_type']}"));
                break;
        }
        return call_user_func_array($render, [$data]);
    }

    protected static function renderHtmlMsg($data){

    }

    protected static function renderTplMsg($data){
        $tplContent = '';
        if(empty($data['tpl_content'])){
            $messageTpl = MessageTplQuery::find()->andWhere(['mtpl_code' => $data['tpl_code']])->one();
            if(!$messageTpl){
                throw new \Exception(Yii::t('app', "数据不存在"));
            }
            $tplContent = $messageTpl->mtpl_content;
        }else{
            $tplContent = $data['tpl_content'];
        }
        $globalParams = [
            'u_username' => 'lartik'
        ];
        $globalParams['current_time'] = date('Y-m-d H:i:s', time());
        $paramsMap = array_merge($globalParams, $data['tpl_params']);
        $content = preg_replace_callback('/(%(.+?)%)/', function($matches) use ($paramsMap){
            if(array_key_exists($matches[2], $paramsMap)){
                return $paramsMap[$matches[2]];
            }else{
                throw new \Exception(Yii::t('app', "不存在模板变量定义的{$matches[2]}"));
            }
        }, $tplContent);
        return $content;
    }

    public function pullUserMsg(User $user){
        $newBoardMsges = MsgQuery::findUnInsertBoardMsg(['=', 'u_id', $user->u_id])
                                  ->asArray()->all();
        $unreadMsg = [];
        foreach($newBoardMsges as $boardMsg){
            $unreadMsg[] = [
                'u_id' => $user->u_id,
                'um_msg_id' => $boardMsg['mm_id'],
                'um_status' => UserMessage::STATUS_UNREAD,
                'um_from_uid' => $boardMsg['mm_create_uid'],
                'um_type' => $boardMsg['mm_create_uid'],
                'um_content_type' => $boardMsg['mm_content_type'],
                'um_content' => static::buildFinalContent([
                    'tpl_code' => $boardMsg['mm_tpl_code'],
                    'tpl_params' => json_decode($boardMsg['mm_vars'], true),
                    'tpl_content' => $boardMsg['mm_content'],
                    'content' => $boardMsg['mm_content'],
                    'content_type' => $boardMsg['mm_content_type']
                ]),
                'um_created_at' => $boardMsg['mm_created_time'],
            ];
        }
        return $this->batchCreateUnreadUsrMsg($unreadMsg);
    }

    protected static function renderPlainMsg($data){
        return $data['content'];
    }

    public function createTpl($data){
        $data['mtpl_created_at'] = time();
        return Yii::$app->db->createCommand()->insert(MessageTpl::tableName(), $data)->execute();
    }

    protected function createUnreadBoardMsg($data){
        $boardMsg = new MessageMap();
        if(!$boardMsg->load($data, '') || !$boardMsg->validate()){
            $this->addError('', $this->getOneErrMsg($boardMsg));
            return false;
        }
        $boardMsg->mm_created_time = time();
        if(!$boardMsg->insert(false)){
            $this->addError(Errno::DB_INSERT_FAIL, Yii::t('app', "插入广播消息失败"));
            return false;
        }
        return $boardMsg;
    }
    public function batchCreateUnreadUsrMsg($data){
        return Yii::$app->db->createCommand()->batchInsert(
            UserMessage::tableName(),
            ['u_id','um_msg_id','um_status','um_from_uid','um_type','um_content_type','um_content','um_created_at'],
            $data
        )->execute();
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
