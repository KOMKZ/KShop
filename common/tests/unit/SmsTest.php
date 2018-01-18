<?php
namespace common\tests;
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
        $smsData = [
            'sms_type' => Sms::TYPE_PRIVATE,
            'sms_inner_code' => 'user_register_succ',
            'sms_params_object' => [
                'name' => 'kitralzhong'
            ]
        ];
        $smsModel = new SmsModel();
        $result = $smsModel->saveWaitSend($smsData);
        Debug::debug($smsModel->getErrors());
    }
}
