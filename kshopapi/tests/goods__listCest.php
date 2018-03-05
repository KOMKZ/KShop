<?php
use Codeception\Util\Debug;
use yii\helpers\ArrayHelper;

class goods__listCest
{
    private $_jwt = '';
    public function _before(ApiTester $I)
    {
        $I->sendPOST('/auth/login', [
            'u_email' => 'kitralzhong0@qq.com',
            'password' => 'philips',
            'type' => 'token'
        ]);
        $res = $this->getResData($I);
        $this->_jwt = $res['data']['jwt'];
    }

    public function _after(ApiTester $I)
    {
    }

    private function getResData($I){
        $res = json_decode($I->grabResponse(), true);
        return $res;
    }

    // tests
    public function tryToTest(ApiTester $I)
    {
        $I->haveHttpHeader("Authorization", "Bearer " . $this->_jwt);
        $I->sendPOST('/goods/list');
        Debug::debug($this->getResData($I));
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['code' => 0]);
    }
}
