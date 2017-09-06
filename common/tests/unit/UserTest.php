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
    public function testCreateReceiverAddr(){
        Yii::$app->db->beginTransaction();

        $data = [
            'rece_location_id' => '19:1607:3155',
            'rece_name' => "lartikzhong",
            'rece_location_string' => "半岛花园A区",
            'rece_contact_number' => '13715194169',
        ];
        $uModel = new UserModel();
        $user = UserQuery::findActive()->where(['u_id' => 1])->one();
        $result = $uModel->createUserReceiverAddr($user, $data);
        if(!$result){
            $this->debug($uModel->getOneError());
        }
        console($result->toArray());
    }
    public function testCreateBill(){
        return true;
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
