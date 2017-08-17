<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\pay\billparser\AliBillParser;
use common\models\pay\billparser\WxBillParser;

/**
 *
 */
class PayController extends Controller{
    public function actionDownloadAliBill(){
        $alipay = Yii::$app->alipay;
        $filter = [
            'date' => '2017-07',
            'type' => 'trade'
        ];
        $file = $alipay->billDownload($filter);
        if(!$file){
            list($code, $error) = $alipay->getOneError();
            echo $error . "\n";
            exit();
        }
        $parser = new AliBillParser();
        $parser->parseTrade($file, [function($row, $key, $customParams){
            echo "\t" . $row['out_trade_no'] . ' ' . ($row['total_amount']/100) . ' ' . $row['goods_title'] . "\n";
        }, ['app_type' => 'hse']], 'zip');
    }

    public function actionDownloadWxBill(){
        $wxpay = Yii::$app->wxpay;
        $parser = new WxBillParser();
        $year = 2017;
        $month = 8;
        $current=sprintf("%d-%02d", $year, $month);
        while($current != '2017-09'){
            echo $current . "\n";
            $filter = ['date' => $current, 'type' => 'ALL'];
            $file = $wxpay->billDownload($filter);
            $parser->parseTrade($file, [function($row, $key, $customParams){
                echo "\t" . $row['out_trade_no'] . ' ' . ($row['total_amount']/100) . ' ' . $row['goods_title'] . "\n";
            }, ['app_type' => 'hse']], 'csv');
            if($month >= 12){
                $month = 1;
                $year++;
            }else{
                $month++;
            }
            $current=sprintf("%d-%02d", $year, $month);
        }
    }

}
