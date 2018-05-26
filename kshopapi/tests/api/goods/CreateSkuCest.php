<?php
use Codeception\Util\Debug;

class CreateSkuCest
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
        $goods = [
            'g_code' => 'A10001',
            'g_vaild_sku_ids' => [
                ['value' => '6:1'],
                ['value' => '6:2'],
                ['value' => '6:3'],
                ['value' => '6:4']
            ]
        ];
        $I->setAuthHeader();
		foreach($goods['g_vaild_sku_ids'] as $item){
			$I->sendPOST(sprintf('/goods/%s/sku', $goods['g_code']), [
				'g_code' => $goods['g_code'],
				'g_sku_value' => $item['value'],
				'g_sku_price' => 10000,
                'g_sku_status' => 'sale',
				'g_sku_stock_num' => 10,
			]);
			$I->seeResponseCodeIs(200);
			$I->seeResponseContainsJson([
				'code' => 0,
				'data' => [
                    'g_sku_status' => 'sale',
					'g_sku_value' => $item['value'],
					"g_sku_price" => 10000,
					'g_sku_stock_num' => 10,
				]
			]);
		}
	}
}
