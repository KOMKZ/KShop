tar<?php
use Codeception\Util\Debug;
use yii\helpers\ArrayHelper;

class user__viewCest
{

    public function _before(ApiTester $I)
    {

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
        $I->sendGET('/user/view', [
            'u_id' => 210
        ]);
        Debug::debug($this->getResData($I));
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['code' => 0]);
    }
}
