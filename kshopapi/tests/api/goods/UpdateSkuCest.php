<?php
use Codeception\Util\Debug;

class UpdateSkuCest
{
    public function _before(ApiTester $I)
    {
        $I->sendPOST('/auth/login', [
            'u_email' => 'kitralzhong2@qq.com',
            'password' => 'philips',
            'type' => 'token'
        ]);
        $res = $this->getResData($I);
        $I->jwt = $res['data']['jwt'];
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
        $I->setAuthHeader();
		$I->sendPUT(sprintf('/goods/%s/sku/%s', 'A10001', '6:1'), [
			'g_sku_price' => 12500,
            'g_sku_status' => 'sale',
			'g_sku_stock_num' => 100,
		]);
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			'code' => 0,
			'data' => [
				'g_sku_price' => 12000,
	            'g_sku_status' => 'sale',
				'g_sku_stock_num' => 100,
			]
		]);
	}
}
