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
    public function tryToTest(ApiTester $I)
    {
        $I->setAuthHeader();

        $I->sendPost("/goods", [
            'g_cls_id' => 2,
            'g_primary_name' => '德式轻量保温杯',
            'g_code' => 'A10001',
            'g_intro_text' => '德式轻量保温杯 简单介绍',
            'g_metas' => [
                ['g_atr_id' => 1, 'gm_value' => "内层：医用级316不锈钢\n外层：食品级304不锈钢"],
                ['g_atr_id' => 2, 'gm_value' => "容量：500ml\n规格：6.5*6.5*22cm"],
                ['g_atr_id' => 3, 'gm_value' => "GB/T29606-2013不锈钢真空杯"],
                ['g_atr_id' => 4, 'gm_value' => "6小时≥68℃"],
                ['g_atr_id' => 5, 'gm_value' => "1、保温杯内放有干竹炭干燥剂，运输过程可能会破损造成竹炭粉散落在杯盖和杯内，新杯使用前请用清水或温水洗净，请您放心使用。"],
                ['g_atr_name' => "logo标志", "gm_value" => "严选"],
            ],
            'g_sku_attrs' => [
                [
                    'g_atr_id' => 6,
                    'g_atr_opts' => [
                        ['g_opt_name' => '粉红色'],
                        ['g_opt_name' => '灰色'],
                        ['g_opt_name' => '黑色'],
                        ['g_opt_name' => '金色'],
                    ]
                ]
            ]
        ]);
        // $res = $I->getResData();
        $I->seeResponseCodeIs(200);
        $res = $this->getResData($I);
        $goods = $res['data'];
        Debug::debug($goods);

        $I->seeResponseContainsJson([
            'code' => 0,
            'data' => [
                'g_cls_id' => "2",
                'g_code' => 'A10001',
                'g_primary_name' => '德式轻量保温杯',
                'g_intro_text' => '德式轻量保温杯 简单介绍',
                'g_metas' => [
                    ['g_atr_id' => 1, 'gm_value' => "内层：医用级316不锈钢\n外层：食品级304不锈钢"],
                    ['g_atr_id' => 2, 'gm_value' => "容量：500ml\n规格：6.5*6.5*22cm"],
                    ['g_atr_id' => 3, 'gm_value' => "GB/T29606-2013不锈钢真空杯"],
                    ['g_atr_id' => 4, 'gm_value' => "6小时≥68℃"],
                    ['g_atr_id' => 5, 'gm_value' => "1、保温杯内放有干竹炭干燥剂，运输过程可能会破损造成竹炭粉散落在杯盖和杯内，新杯使用前请用清水或温水洗净，请您放心使用。"],
                    ['g_atr_name' => "logo标志", "gm_value" => "严选"],
                ],
                'g_sku_attrs' => [
                    [
                        'g_atr_opts' => [
                            ['g_opt_name' => '粉红色'],
                            ['g_opt_name' => '灰色'],
                            ['g_opt_name' => '黑色'],
                            ['g_opt_name' => '金色'],
                        ],
                        'g_atr_name' => '颜色'
                    ]
                ]
            ],
        ]);






    }
}
