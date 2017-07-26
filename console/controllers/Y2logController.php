<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class Y2logController extends Controller{

    public function actionIndex($last = "30s", $app = 'app', $logFile = 'app.log'){
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
        $file = Yii::getAlias(sprintf('@%s/runtime/logs/%s', $app, $logFile));
        $tmpFile = '/tmp/y2log_' . time() . '.txt';
        $tmpFp = fopen($tmpFile, 'a+');
        $fp = fopen($file, 'r');
        $pos = -2; // Skip final new line character (Set to -1 if not present)
        $currentLine = '';
        $oneException = [];
        while (-1 !== fseek($fp, $pos, SEEK_END)) {
            $char = fgetc($fp);
            if (PHP_EOL == $char) {
                if(preg_match('/^([0-9\-]+\s[0-9:]+)/', $currentLine, $matches)){
                    $time = strtotime($matches[1]);
                    if($time >= $begin){
                        array_unshift($oneException, $currentLine);
                        $this->fwrite_stream($tmpFp, implode("\n", $oneException) . "\n");
                        $oneException = [];
                    }else{
                        return ;
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
        if(preg_match('/^([0-9\-]+\s[0-9:]+)/', $currentLine, $matches)){
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
                if(preg_match('/^([0-9\-]+\s[0-9:]+)/', $currentLine, $matches)){
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
        if(preg_match('/^([0-9\-]+\s[0-9:]+)/', $currentLine, $matches)){
            array_unshift($oneException, $currentLine);
            echo implode("\n", $oneException). "\n";
            $oneException = [];
        }else{
            array_unshift($oneException, $currentLine);
        }
        fclose($tmpFp);
        unlink($tmpFile);
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
