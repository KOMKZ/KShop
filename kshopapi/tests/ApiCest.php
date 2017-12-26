<?php
use Codeception\Util\Debug;

class ApiCest
{
	public function tryApi(ApiTester $I)
	{
		$I->sendPOST('/auth/login', [
			'u_email' => '784248377@qq.com',
			'password' => 'philips',
			'type' => 'token'
		]);
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$res = json_decode($I->grabResponse(), true);
		$payload = $res['data']['jwt'];
		sleep(5);

		$max = 100;
		$i = 1;
		while($i <= $max){
			$I->haveHttpHeader("Authorization", "Bearer " . $payload);
			$I->sendPOST('/user/filter', [
				'name' => 'davert',
				'email' => 'davert@codeception.com'
			]);
			$I->seeResponseCodeIs(200);
			$I->seeResponseIsJson();
		}
	}
}
