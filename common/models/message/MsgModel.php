<?php
namespace common\models\message;

use Yii;
use common\models\Model;
use common\models\message\ar\MessageMap;
use common\models\user\ar\UserMessage;
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
        $messageTpl = MessageTplQuery::find()->andWhere(['mtpl_code' => $msg])->one();
        if(!$messageTpl){
            throw new \Exception(Yii::t('app', "数据不存在"));
        }
        $globalParams = [
            'u_username' => 'lartik'
        ];
        $globalParams['current_time'] = date('Y-m-d H:i:s', time());
        $paramsMap = array_merge($globalParams, $msg->tpl_params);
        $content = preg_replace_callback('/(%(.+?)%)/', function($matches) use ($paramsMap){
            if(array_key_exists($matches[2], $paramsMap)){
                return $paramsMap[$matches[2]];
            }else{
                throw new \Exception(Yii::t('app', "不存在模板变量定义的{$matches[2]}"));
            }
        }, $messageTpl->mtpl_content);
        console($content);
    }


    protected static function renderPlainMsg(Message $msg){
        return $msg->content;
    }

    public function createTpl($data){
        $data['mtpl_created_at'] = time();
        return Yii::$app->db->createCommand()->insert(MessageTpl::tableName(), $data)->execute();
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
