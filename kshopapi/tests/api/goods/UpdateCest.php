<?php
use Codeception\Util\Debug;

class UpdateCest
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
        $I->sendPUT(sprintf("/goods/%s", 'A10001'), [
            'g_primary_name' => '德式轻量保温杯(新款)',
            'g_secondary_name' => '德式轻量保温杯(新款)2018新款',
            'g_intro_text' => '德式轻量保温杯(新款)2018新款 长长的介绍文本',
            'g_metas' => [
                ['gm_id' => 2, 'gm_value' => "内层：医用级316不锈钢\n外层：食品级304不锈钢(顶级)"],
                ['g_atr_name' => "明星代言人", "gm_value" => "lartik"],
            ],
            'g_sku_attrs' => [
                [
                    'gr_id' => 1,
                    'g_atr_opts' => [
                        ['g_opt_id' => 1, 'g_opt_name' => '红粉色'],
                        ['g_opt_name' => '黄色']
                    ]
                ]
            ]
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'code' => 0,
            'data' => [
                'g_primary_name' => '德式轻量保温杯(新款)',
                'g_secondary_name' => '德式轻量保温杯(新款)2018新款',
                'g_intro_text' => '德式轻量保温杯(新款)2018新款 长长的介绍文本',
                'g_metas' => [
                    ['gm_id' => 2, 'gm_value' => "内层：医用级316不锈钢\n外层：食品级304不锈钢(顶级)"],
                    ['g_atr_name' => "明星代言人", "gm_value" => "lartik"],
                ],
                'g_sku_attrs' => [
                    [
                        'g_atr_opts' => [
                            ["g_opt_name" => "红粉色"],
                            ["g_opt_name" => "灰色"],
                            ["g_opt_name" => "黑色"],
                            ["g_opt_name" => "金色"],
                        ],
                        'g_atr_name' => '颜色'
                    ]
                ]

            ]
        ]);
    }
}
