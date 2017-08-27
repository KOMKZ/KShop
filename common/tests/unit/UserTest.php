<?php
namespace common\tests;
use Yii;
use common\models\user\UserModel;
use common\models\user\ar\User;
use common\models\trans\query\TransactionQuery;
use common\models\user\query\UserQuery;
use common\models\user\query\UserBillQuery;
use common\models\user\ar\UserBillRecord;

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
    public function testCreateBill(){
        $trans = TransactionQuery::find()->where(['t_id' => 1])->one();
        $user = UserQuery::findActive()->where(['u_id' => 1])->one();
        $uModel = new UserModel();
        $extra = [];
        $bill = $uModel->createUserBill($user, $trans, $extra);
        if(!$bill){
            $this->debug($uModel->getOneError());
        }
        $one = UserBillQuery::find()->orderBy(['u_bill_id' => SORT_DESC])->one();
        console($one->toArray());
    }
    public function testCreate(){
        return ;
        // Yii::$app->db->beginTransaction();
        $data = [
            'u_username' => 'lartik',
            'password' => 'philips',
            'password_confirm' => 'philips',
            'u_email' => '784248377@qq.com',
            'u_auth_status' => User::HAD_AUTH,
            'u_status' => User::STATUS_ACTIVE,
        ];
        $uModel = new UserModel();
        $user = $uModel->createUser($data);
        if(!$user){
            console($uModel->getOneError());
        }

        console($user->toArray());
    }
}
