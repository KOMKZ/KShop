<?php
namespace common\models\email;

use Yii;
use common\base\Model;
use common\models\email\EmailModel;


/**
 *
 */
class Mail extends Model
{
    static private $mailer;

    public $subject;
    public $params;
    public $template;
    public $from;
    public $to;
    public $body;
    public $img = [];
    public $attatch = [];
    public $defaultSender = '';
    public $defaultSenderPwd = '';

    public function send(){
        try {
            $this->prepareForSend();
            if(!self::$mailer->send()){
                $this->addError('', Yii::t('app', self::$mailer->ErrorInfo));
                return false;
            }
            return true;
        } catch (\Exception $e) {
            Yii::error($e);
            $this->addError('', $e->getMessage());
            return false;
        }
    }

    public function check(){
        $require = ['subject', 'to', 'body'];
        foreach($require as $name){
            if(empty($this->$name)){
                $this->addError('', Yii::t('app', "$name不能为空"));
                return false;
            }
        }
        return true;
    }
    protected function prepareForSend(){
        $this->buildMailer($this->defaultSender, $this->defaultSenderPwd);
        $this->buildMsg();
    }
    private function buildMsg(){

        $this->from = $this->from ? $this->from : $this->defaultSender;
        self::$mailer->setFrom($this->from);
        self::$mailer->addAddress($this->to);
        self::$mailer->Subject = $this->subject;
        self::$mailer->isHTML(true);
        list($msgBody, $contentType) = $this->body;
        if(empty($msgBody) && $this->template){
            $tpl = EmailModel::getTpl($this->template);
            $content = Yii::$app->view->renderFile($tpl['path'], $this->params);
            $msgBody = Yii::$app->view->renderFile($tpl['layout'], ['content' => $content]);
            $this->img = array_merge($this->img, $tpl['img']);
        }
        // 看看是不是需要嵌入图片
        if(!empty($this->img) && 'text/html' == $contentType){
            $imgMap = [];
            foreach($this->img as $key => $imgPath){
                $id = '';
                if(file_exists($imgPath)){
                    $id = self::$mailer->AddEmbeddedImage($imgPath, $key);
                }else{
                    // log
                    $id = '#';
                }
                $imgMap[$key] = $id;
            }
        }

        self::$mailer->Body = $msgBody;
    }
    private function buildMailer($sender, $pwd){
        if(self::$mailer){
            return self::$mailer;
        }
        // php mailer
        $mail = new \PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.qq.com';
        $mail->SMTPAuth = true;
        $mail->Username = $sender;
        $mail->Password = $pwd;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        return self::$mailer = $mail;
    }

}
