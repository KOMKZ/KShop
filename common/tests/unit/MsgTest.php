<?php
namespace common\tests;
use Yii;



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

    public function testCreate(){
        $msgModel = new MsgModel();
        $data = [
            'type' => ''
        ];
        $message = $msgModel->createMessage($data);
        if(!$message){
            $this->debug($msgModel->getOneError());
        }
        console($message);
    }

}
