<?php
namespace common\models\email;

use Yii;
use common\base\Model;
use common\models\email\tables\EmailFailed;
use common\models\email\Mail;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use yii\data\ActiveDataProvider;


/**
 *
 */
class EmailModel extends Model
{
    static private $amqpConn;
    static private $Channel;

    public static function insertFailedEmail($data, $code = null, $message = null){
        $table = EmailFailed::tableName();
        $sql = "
            insert into $table
            (`emf_id`, `emf_data`, `emf_code`, `emf_message`, `emf_created_at`)
            values
            (null, :p2, :p3, :p4, :p5)
        ";
        $sqlCommand = Yii::$app->db->createCommand($sql, [
            ':p2' => is_string($data) ? $data : json_encode($data),
            ':p3' => $code,
            ':p4' => is_string($message) ? $message : json_encode($message),
            ':p5' => time()
        ]);
        $sqlCommand->execute();
    }

    public function getOneFailEmail($condition){
        if(!empty($condition)){
            return EmailFailed::find()->where($condition)->one();
        }else{
            return null;
        }
    }

    public function getFailedEmailProvider($condition = [], $sortData = [], $withPage = true, $table = null){
        $query = EmailFailed::find();
        // $query = $this->buildQueryWithCondition($query, $condition);

        $defaultOrder = [
            'emf_created_at' => SORT_DESC
        ];

        if(!empty($sortData)){
            $defaultOrder = $sortData;
        }
        $pageConfig = [];
        if(!$withPage){
            $pageConfig['pageSize'] = 0;
        }else{
            $pageConfig['pageSize'] = 10;
        }
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pageConfig,
            'sort' => [
                'attributes' => ['emf_created_at'],
                'defaultOrder' => $defaultOrder
            ]
        ]);
        $pagination = $provider->getPagination();
        return [$provider, $pagination];
    }


    public function sendEmail($data, $asyc = true){
        if($asyc && $this->isAsycSendOk()){
            return $this->sendEmailAsyc($data);
        }else{
            return $this->sendEmailSyc($data);
        }
    }

    public static function getTpl($name = null){
        $map = [
            'signup-user-auth-email' => [
                'path' => Yii::getAlias('@common/mail/signup-user-auth-email.php'),
                'layout' => Yii::getAlias('@common/mail/layouts/html.php'),
                'img' => [
                    'tpl_img' => '/home/kitral/Pictures/2.jpg',
                ]
            ],
        ];
        return $name ? $map[$name] : $map;
    }

    protected function isAsycSendOk(){
        return true;
    }
    protected function sendEmailAsyc($data){
        $connection = $this->getAmqpConn();
        $channel = $this->getChannel();
        $msg = new AMQPMessage(json_encode($data), ['delivery_mode' => 2]);
        $channel->basic_publish($msg, '', 'email-job');
        return true;
    }

    /**
     * [sendEmailSyc description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected function sendEmailSyc($data){
        $data['class'] = Mail::className();
        $mail = Yii::createObject($data);
        if($mail->check() && $mail->send()){
            return true;
        }else{
            // todo
            $this->addErrors($mail->getErrors());
            return false;
        }
    }


    private function getChannel(){
        if(self::$Channel){
            return self::$Channel;
        }
        $conn = $this->getAmqpConn();
        $channel = $conn->channel();
        $channel->queue_declare('email-job', false, true, false, false);
        return self::$Channel = $channel;
    }

    private function getAmqpConn(){
        if(self::$amqpConn){
            return self::$amqpConn;
        }
        return self::$amqpConn = new AMQPStreamConnection('localhost', 5672, '', '');
    }
}
