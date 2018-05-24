<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;



class TestController extends Controller{
    public function actionClear(){
        // 清空数据库
        list(, $dbName) = explode("dbname=", Yii::$app->db->dsn);
        $all[] = "SET FOREIGN_KEY_CHECKS = 0;";
        foreach( Yii::$app->db->createCommand("
        SELECT CONCAT('truncate table ',table_schema,'.',table_name,';') as cmd
          FROM information_schema.tables
         WHERE table_schema IN ('{$dbName}');
        ")->queryAll() as $item){
            $all[] = $item['cmd'];
        }
        $all[] = "SET FOREIGN_KEY_CHECKS = 1;";

        $clear = implode("\n", $all);
        Yii::$app->db->createCommand($clear)->execute();

        // 重新安装数据
        system(sprintf("%s/yii_test data/inst-test-user", ROOT_PATH));
        system(sprintf("%s/yii_test data/inst-test", ROOT_PATH));
        system(sprintf("%s/yii_test rbac/install-rbac-data", ROOT_PATH));
    }
}
