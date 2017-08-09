<?php
namespace common\tests;
use Yii;
use common\models\mail\MailModel;
use common\models\mail\ar\Mail;
use common\models\mail\query\MailQuery;


class MailTest extends \Codeception\Test\Unit
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

    public function debug($data){
        console($data);
    }
    public function testSend(){
        // return ;
        Yii::$app->db->beginTransaction();
        $mail = MailQuery::find()->where(['mail_id' => 20])->one();
        $mailModel = new MailModel();
        $result = $mailModel->send($mail, true);
        if(!$result){
            console($this->debug($mailModel->getOneError()));
        }
        console($result);
    }
    public function testCreate(){
        return ;
        Yii::$app->db->beginTransaction();
        $mailModel = new MailModel();


        $data = [
            'mail_meta_data' => [
                'addresses' => [
                    'type' => Mail::LIST_TYPE_INLINE,
                    'list' => [
                        '784248377@qq.com',
                        'm13715194169_1@163.com'
                    ]
                ]
            ],
            'mail_title' => '测试邮件',
            'mail_content' => "测试邮件内容",
            'mail_content_type' => Mail::CONTENT_TYPE_HTML,
            'mail_create_uid' => 1,
            'mail_is_cron' => 0,
            'mail_attachments' => [
                ['file' => 'disk:test/7b924ab4b3013136909d1d8483cfc6cf.jpg'],
                ['file' => 'disk:test/80133f06f3827d3b2d8ce0624650cc10.jpg'],
                ['file' => 'disk:test/83ea3bddcbb3b44700e99bf6fe05bdb2.jpg']
            ]
        ];
        $mail = $mailModel->createMail($data);
        if(!$mail){
            $this->debug($mailModel->getOneError());
        }
        console($mail->toArray());
    }
}
