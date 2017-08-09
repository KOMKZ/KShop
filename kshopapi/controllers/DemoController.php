<?php
namespace kshopapi\controllers;

use yii\web\Controller;

/**
 *
 */
class DemoController extends Controller
{
    public function actionIndex(){
        ini_set('memory_limit', '-1');


        $sender = "";
        $pwd = "";

        $mail = new \PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.qq.com';
        $mail->SMTPAuth = true;
        $mail->Username = $sender;
        $mail->Password = $pwd;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom($sender);
        $mail->addAddress('784248377@qq.com');
        $mail->addAddress('m13715194169_1@163.com');
        $mail->Subject = "测试邮件";
        $mail->CharSet = "utf-8";
        $mail->isHTML(true);
        // $tpl = EmailModel::getTpl($this->template);
        // $content = Yii::$app->view->renderFile($tpl['path'], $this->params);
        // $msgBody = Yii::$app->view->renderFile($tpl['layout'], ['content' => $content]);

        $content = file_get_contents("/home/master/tmp/1.html");//"<p>hello world<img src='http://oss.hsehome.org/trstps_data/7728464daf24c2abb2d1929562321b9c270014cba.png'></p>";

        // $imgs = [
        //     'img03.jpg' => '/home/master/tmp/test.jpg'
        // ];
        // // 看看是不是需要嵌入图片
        // $imgMap = [];
        // foreach($imgs as $key => $imgPath){
        //     $id = '';
        //     if(file_exists($imgPath)){
        //         $id = $mail->addAttachment($imgPath, $key);
        //     }else{
        //         // log
        //         $id = '#';
        //     }
        //     $imgMap[$key] = $id;
        // }
        $mail->Body = $content;

        if(!$mail->send()){
            console($mail->ErrorInfo);
            return false;
        }
    }
}
