<?php
namespace common\tests;

use Yii;
use common\models\message\MsgModel;
use common\models\message\Message;
use common\models\user\query\UserQuery;
use common\models\message\query\MsgQuery;


class MsgTest extends \Codeception\Test\Unit
{

    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {

    }
    protected function _after()
    {
    }

    public function debug($data){
        console($data);
    }

    public function testGetMsg(){
        // return ;
        // Yii::$app->db->beginTransaction();
        $user = UserQuery::findActive()->andWhere(['=', 'u_id', 1])->one();
        $msgModel = new MsgModel();
        $result = $msgModel->pullUserMsg($user);
        if(false === $result){
            $this->debug($msgModel->getOneError());
        }
        $result = MsgQuery::findUnReadUsrMsg()->andWhere(['=', 'u_id', 1])->orderBy(['um_created_at' => SORT_DESC])->asArray()->all();
        console($result);
    }

    public function testCreateTpl(){
        return ;
        Yii::$app->db->beginTransaction();
        $msgModel = new MsgModel();
        // $data = [
        //     'mtpl_code' => 'register_welcome',
        //     'mtpl_content' => "欢迎注册KSHOP, %u_username%!"
        // ];
        $data = [
            'mtpl_code' => 'welcome_home',
            'mtpl_content' => "欢迎来到KSHOP, %u_username%!"
        ];
        $result = $msgModel->createTpl($data);
        if(!$result){
            $this->debug($msgModel->getOneError());
        }
        console($result);
    }

    public function testCreateTplBoardMsg(){
        return ;
        Yii::$app->db->beginTransaction();
        $msgModel = new MsgModel();
        $data = [
            'type' => Message::TYPE_BOARD,
            'content_type' => Message::CONTENT_TYPE_TEMPLATE,
            'tpl_code' => 'welcome_home',
            'create_uid' => 2
        ];
        $message = $msgModel->createMessage($data);
        if(!$message){
            $this->debug($msgModel->getOneError());
        }
        $result = $msgModel->send($message);
        if(!$result){
            $this->debug($msgModel->getOneError());
        }
    }
    public function testCreateTplMsg(){
        return ;
        Yii::$app->db->beginTransaction();
        $msgModel = new MsgModel();
        $data = [
            'type' => Message::TYPE_ONE,
            'content_type' => Message::CONTENT_TYPE_TEMPLATE,
            'tpl_code' => 'register_welcome',
            'create_uid' => 1,
            'receipt_uid' => 2,
        ];
        $message = $msgModel->createMessage($data);
        if(!$message){
            $this->debug($msgModel->getOneError());
        }
        $result = $msgModel->send($message);
        if(!$result){
            $this->debug($msgModel->getOneError());
        }
        console($result);
    }
    public function testCreate(){
        return ;
        Yii::$app->db->beginTransaction();
        $msgModel = new MsgModel();
        $data = [
            'type' => Message::TYPE_ONE,
            'content' => '你是不是也是一个人在小屋子里写代码.',
            'content_type' => Message::CONTENT_TYPE_PLAIN,
            'create_uid' => 1,
            'receipt_uid' => 2,
        ];
        $message = $msgModel->createMessage($data);
        if(!$message){
            $this->debug($msgModel->getOneError());
        }
        $result = $msgModel->send($message);
        if(!$result){
            $this->debug($msgModel->getOneError());
        }
        console(1);
    }

}
