<?php
use Codeception\Util\Debug;

class QuerySourceCest
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
		$i->sendGET("/goods/A10001/source", [
            'gs_use_type' => 'sku_m',
        ]);
		$i->seeResponseCodeIs(200);
		$i->seeResponseContainsJson([
			"code" => 0,
			"data" => [
				['gs_name' => 'gold.png']
			]
		]);
        $res = $this->getResData($i);
        Debug::debug($res);

	}
}
