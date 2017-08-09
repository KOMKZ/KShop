<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class Y2logController extends Controller{

    public function init(){
        parent::init();
        if(!empty(Yii::$app->params['y2log_alias'])){
            foreach(Yii::$app->params['y2log_alias'] as $name => $path){
                Yii::setAlias($name, $path);
            }
        }
    }
    public function actionIndex($last = "30s", $app = '@app', $fileLog = 'app.log'){
        date_default_timezone_set("Asia/Shanghai");
        if(preg_match('/^([0-9]+)([smhd]{1})/', $last, $matches)){
            $timeUnit = $matches[2];
            $offsetValue = $matches[1];
            switch ($timeUnit) {
                case 'd':
                    $offset =  3600*24*$offsetValue;
                    break;
                case 'h':
                    $offset =  3600*$offsetValue;
                    break;
                case 'm':
                    $offset =  60*$offsetValue;
                    break;
                case 's':
                    $offset =  $offsetValue;
                    break;
                default:
                    throw new \Exception('unsupported format '. $timeUnit);
                    break;
            }
            $begin = time() - $offset;
            $end = time() + 30;
        }else{
            $this->help();
            exit();
        }
        if(1 != $fileLog){
            $file = Yii::getAlias('@' == $app[0] ? sprintf('%s/runtime/logs/%s', $app, $fileLog) : $app);
        }else{
            $file = Yii::getAlias($app);
        }
        $tmpFile = '/tmp/y2log_' . time() . '.txt';
        touch($tmpFile);
        $tmpFp = fopen($tmpFile, 'r+');
        if(substr_compare($file, 'ssh2.sftp', 0, 8) == 0){
            $urlInfo = parse_url($file);
            $session = ssh2_connect($urlInfo['host'], $urlInfo['port']);
            ssh2_auth_password($session, $urlInfo['user'], $urlInfo['pass']);
            $stream = ssh2_sftp($session);
            $remoteFile = "ssh2.sftp://$stream" . $urlInfo['path'];
            $file = Yii::getAlias('@app/runtime/y2log.txt');
            file_put_contents($file, file_get_contents($remoteFile));
            chmod($file, 0600);
            unset($stream);
            unset($session);
        }
        $fp = fopen($file, 'r');
        $pos = -2; // Skip final new line character (Set to -1 if not present)
        $currentLine = '';
        $oneException = [];
        $pattern = '/^([0-9\-\/]+\s[0-9:]+)/';
        while (-1 !== fseek($fp, $pos, SEEK_END)) {
            $char = fgetc($fp);
            if (PHP_EOL == $char) {
                if(preg_match($pattern, $currentLine, $matches)){
                    $time = strtotime($matches[1]);
                    if($time >= $begin){
                        array_unshift($oneException, $currentLine);
                        $this->fwrite_stream($tmpFp, implode("\n", $oneException) . "\n");
                        $oneException = [];
                    }else{
                        break ;
                    }
                }else{
                    array_unshift($oneException, $currentLine);
                }
                $currentLine = '';
            } else {
                $currentLine = $char . $currentLine;
            }
            $pos--;
        }
        if(preg_match($pattern, $currentLine, $matches)){
            $time = strtotime($matches[1]);
            if($time >= $begin){
                array_unshift($oneException, $currentLine);
                $this->fwrite_stream($tmpFp, implode("\n", $oneException) . "\n");
                $oneException = [];
            }
        }else{
            array_unshift($oneException, $currentLine);
        }
        fclose($fp);
        rewind($tmpFp);

        $pos = -2; // Skip final new line character (Set to -1 if not present)
        $currentLine = '';
        $oneException = [];
        while (-1 !== fseek($tmpFp, $pos, SEEK_END)) {
            $char = fgetc($tmpFp);
            if (PHP_EOL == $char) {
                if(preg_match($pattern, $currentLine, $matches)){
                    array_unshift($oneException, $currentLine);
                    echo implode("\n", $oneException). "\n";
                    $oneException = [];
                }else{
                    array_unshift($oneException, $currentLine);
                }
                $currentLine = '';
            } else {
                $currentLine = $char . $currentLine;
            }
            $pos--;
        }
        if(preg_match($pattern, $currentLine, $matches)){
            array_unshift($oneException, $currentLine);
            echo implode("\n", $oneException). "\n";
            $oneException = [];
        }else{
            array_unshift($oneException, $currentLine);
        }
        fclose($tmpFp);

        // unlink($tmpFile);
    }

    public function fwrite_stream($fp, $string) {
        for ($written = 0; $written < strlen($string); $written += $fwrite) {
            $fwrite = fwrite($fp, substr($string, $written));
            if ($fwrite === false) {
                return $written;
            }
        }
        return $written;
    }

    public function help(){
        echo "./yii y2log ([0-9]+)([smhd]{1}) APP_NAME LOG_NAME
example:
./yii y2log 1m kshopapi app.log
";
        exit();
    }

}
