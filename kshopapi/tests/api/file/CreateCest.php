<?php
use Codeception\Util\Debug;

class CreateCest
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
    public function tryToTest(ApiTester $I){
		$I->setAuthHeader();
		$I->sendPOST("/file", [
			'file_category' => 'pub_img',
		], [
			'file' => "/home/lartik/tmp/1.jpg"
		]);
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			'code' => 0
		]);
		$res = $this->getResData($I);
		Debug::debug($res);

	}
}
