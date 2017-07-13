<?php
defined('ROOT_PATH') or define('ROOT_PATH', dirname((dirname(__DIR__))));
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
defined('APP_ONE_CONFIG') or define('APP_ONE_CONFIG', false);


require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');


$config = APP_ONE_CONFIG ?
require(__DIR__ . '/../config/application.php')
:
yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/merge_config.php'),
    require(__DIR__ . '/../config/merge_config.php')
);

(new yii\web\Application($config))->run();
