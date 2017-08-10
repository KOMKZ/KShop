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
        // Yii::$app->db->beginTransaction();
        $mail = MailQuery::find()->where(['mail_id' => 2])->one();
        $mailModel = new MailModel();
        $result = $mailModel->send($mail, false);
        if(!$result){
            console($this->debug($mailModel->getOneError()));
        }
        // console($result);
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
            'mail_title' => '测试邮件' . mt_rand(11111, 99999),
            'mail_content' => "测试邮件内容" . mt_rand(11111, 99999),
            'mail_content_type' => Mail::CONTENT_TYPE_HTML,
            'mail_create_uid' => 1,
            'mail_is_cron' => 0,
            'mail_attachments' => [
                ['file' => '/tmp/1.jpg'],
            ]
        ];
        $mail = $mailModel->createMail($data);
        if(!$mail){
            $this->debug($mailModel->getOneError());
        }
        // console($mail->toArray());
    }
}
