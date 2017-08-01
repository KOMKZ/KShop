<?php
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', __DIR__.'/../../');
defined('ROOT_PATH') or define('ROOT_PATH', dirname((dirname(__DIR__))));

// require('/home/master/pro/php/composer-global-dep/vendor/digitalnature/php-ref/ref.php');
require_once(__DIR__ .  '/../../vendor/autoload.php');
require_once(__DIR__ .  '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../config/bootstrap.php');
$config = require(__DIR__ . '/../config/merge_config.php');
$config['id'] = 'test';
$config['basePath'] = YII_APP_BASE_PATH;
(new yii\console\Application($config));
