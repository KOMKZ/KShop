<?php
namespace common\tests;
use Yii;
use common\models\sms\ar\Sms;
use common\models\sms\SmsModel;
use \Codeception\Util\Debug;
class SmsTest extends \Codeception\Test\Unit
{
    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testSomeFeature()
    {
        Yii::$app->db->beginTransaction();
        $smsData = [
            'sms_type' => Sms::TYPE_PRIVATE,
            'sms_inner_code' => 'user_register_succ',
            'sms_params_object' => [
                'name' => 'kitralzhong',
                'time' => date('y-m-d H:i:s', time())
            ],
            'sms_to_phone' => '13715194169'
        ];
        $smsModel = new SmsModel();
        $result = $smsModel->saveWaitSend($smsData);
        if(!$result){
            Debug::debug([$smsModel->getErrors(), $result]);
        }
        $this->assertFalse($smsModel->hasErrors());
        $this->assertNotEmpty($result);
    }
}
