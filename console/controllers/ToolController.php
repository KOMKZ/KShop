<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class ToolController extends Controller{
    public $is_test = false;
    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['is_test']
        );
    }
    public function actionOneConfig($app){
        $config = ArrayHelper::merge(
            require(Yii::getAlias('@common/config/merge_config.php')),
            require(Yii::getAlias("@{$app}/config/merge_config.php"))
        );
        ksort($config);
        file_put_contents(
            Yii::getAlias(sprintf("@{$app}/config/application%s.php", $this->is_test ? '-test' : '')),
            sprintf("<?php\nreturn %s;", VarDumper::export($config))
        );
    }
    public function actionDecode($string = '', $type = 'json'){
        echo json_decode('"' . $string . '"');
        echo "\n";
    }
}
