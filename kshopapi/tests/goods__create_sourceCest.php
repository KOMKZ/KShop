<?php
use Codeception\Util\Debug;
use common\models\goods\ar\GoodsSource;

class goods__create_sourceCest
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
    public function tryToTest(ApiTester $I)
    {
        $data = [
            'gs_type' => GoodsSource::TYPE_IMG,
            'gs_sid' => 'oss:default/7b8ac394eca4f9cf532374eef1dff1d1.png',
            'gs_name' => '',
            'gs_cls_id' => '264',
            'gs_cls_type' => 'goods',
        ];
        $I->sendPOST('/goods/create-source', $data, ['file' => '/home/master/tmp/abc.png']);
        Debug::debug($this->getResData($I));
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['code' => 0]);

    }
}
