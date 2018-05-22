<?php
use Codeception\Util\Debug;

class GetCest
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
    public function tryToTest(ApiTester $i){
		$i->sendGET("/goods/A10001");
		$i->seeResponseCodeIs(200);
		$i->seeResponseContainsJson([
			"code" => 0,
			"data" => [
				"g_code" => "A10001"
			]
		]);
        $res = $this->getResData($i);
        Debug::debug($res);

	}
}
