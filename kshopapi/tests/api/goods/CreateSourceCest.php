<?php
use Codeception\Util\Debug;

class CreateSourceCest
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
		$I->sendGET("/goods/A10001");
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			'code' => 0,
			'data' => [
				'g_code' => 'A10001'
			]
		]);
        $res = $this->getResData($I);
		$goods = $res['data'];
		// 测试创建商品资源
		$I->sendPOST(sprintf("/source"), [
			'gs_cls_id' => $goods['g_code'],
			'gs_cls_type' => 'goods',
			'gs_type' => 'img'
		], [
			'file' => '/home/lartik/tmp/1.jpg'
		]);
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			'code' => 0,
			'data' => [
				'gs_cls_id' => $goods['g_id']
			]
		]);
	}
}
