<?php
use Codeception\Util\Debug;
use common\models\goods\ar\GoodsSource;

class goods_get_one_sourceCest
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
        $I->sendGET('/goods/view', ['g_id' => 264]);
        $I->seeResponseCodeIs(200);
        $res = $this->getResData($I);
        Debug::debug($res);
    }
}
