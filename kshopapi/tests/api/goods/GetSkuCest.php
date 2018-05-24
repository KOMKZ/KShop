<?php
use Codeception\Util\Debug;

class GetSkuCest
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
		$i->sendGET("/goods/A10001/sku/6:1");
		$i->seeResponseCodeIs(200);
		$i->seeResponseContainsJson([
			"code" => 0,
			"data" => [
				"g_sku_value" => "6:1"
			]
		]);
        $res = $this->getResData($i);
        Debug::debug($res);

	}
}
