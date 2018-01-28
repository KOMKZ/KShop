<?php
use Codeception\Util\Debug;

class goods__listCest
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
        $I->sendPOST('/goods/list');
        Debug::debug($this->getResData($I));
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['code' => 0]);
    }
}
