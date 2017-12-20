<?php
class ApiCest
{
    public function tryApi(ApiTester $I)
    {
        $I->sendGET('/user/filter');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}
