<?php
use Codeception\Util\Debug;
use Yii;

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
        $gs = [
            '@kshopapi/tests/_data/g1.jpg',
            '@kshopapi/tests/_data/g2.jpg',
            '@kshopapi/tests/_data/g3.jpg',
            '@kshopapi/tests/_data/g4.jpg',
        ];

        foreach($gs as $path){
            // 测试创建商品资源
    		$I->sendPOST(sprintf("/goods/%s/source", $goods['g_code']), [
    			'gs_cls_type' => 'goods',
    			'gs_type' => 'img',
                'gs_use_type' => 'goods_m'
    		], [
    			'file' => Yii::getAlias($path)
    		]);
    		$I->seeResponseCodeIs(200);
    		$I->seeResponseContainsJson([
    			'code' => 0,
    			'data' => [
    				'gs_cls_id' => $goods['g_id']
    			]
    		]);
        }

        $skuSource = [
            ['index' => 'A10001-6:1', 'path' => '@kshopapi/tests/_data/pink.png'],
            ['index' => 'A10001-6:2', 'path' => '@kshopapi/tests/_data/grey.png'],
            ['index' => 'A10001-6:3', 'path' => '@kshopapi/tests/_data/black.png'],
            ['index' => 'A10001-6:4', 'path' => '@kshopapi/tests/_data/gold.png'],
        ];
        foreach($skuSource as $item){
            // 测试创建商品资源
    		$I->sendPOST(sprintf("/goods/%s/source", $item['index']), [
    			'gs_cls_type' => 'sku',
    			'gs_type' => 'img',
                'gs_use_type' => 'sku_m'
    		], [
    			'file' => Yii::getAlias($item['path'])
    		]);
    		$I->seeResponseCodeIs(200);
    		$I->seeResponseContainsJson([
    			'code' => 0,
    			'data' => [
                    'gs_name' => basename($item['path'])
    			]
    		]);
        }
	}
}
