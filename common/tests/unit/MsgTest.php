<?php
namespace common\tests;

use Yii;
use common\models\message\MsgModel;
use common\models\message\Message;


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



    public function testCreateTpl(){
        return ;
        Yii::$app->db->beginTransaction();
        $msgModel = new MsgModel();
        $data = [
            'mtpl_code' => 'register_welcome',
            'mtpl_content' => "欢迎注册KSHOP, %u_username%!"
        ];
        $result = $msgModel->createTpl($data);
        if(!$result){
            $this->debug($msgModel->getOneError());
        }
        console($result);
    }

    public function testCreateTplMsg(){
        // return ;
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
        console(1);
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
