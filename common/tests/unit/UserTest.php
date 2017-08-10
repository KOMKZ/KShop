<?php
namespace common\tests;
use Yii;
use common\models\user\UserModel;
use common\models\user\ar\User;


class UserTest extends \Codeception\Test\Unit
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
        // return ;
        Yii::$app->db->beginTransaction();
        $data = [
            'u_username' => 'lartik',
            'password' => md5('philips'),
            'password_confirm' => md5('123456'),
            'u_email' => '784248377@qq.com',
            'u_auth_status' => User::NOT_AUTH,
            'u_status' => User::STATUS_NO_AUTH,
        ];
        $uModel = new UserModel();
        $user = $uModel->createUser($data);
        if(!$user){
            console($uModel->getOneError());
        }

        console($user->toArray());
    }
}
