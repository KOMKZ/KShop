<?php
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', __DIR__ . '/../../console');
defined('ROOT_PATH') or define('ROOT_PATH', dirname((dirname(__DIR__))));

require_once(__DIR__ .  '/../../vendor/autoload.php');
require_once(__DIR__ .  '/../../vendor/yiisoft/yii2/Yii.php');
require(ROOT_PATH . '/common/config/bootstrap.php');
$config = \yii\helpers\ArrayHelper::merge(
	require(ROOT_PATH . '/common/config/merge_config.php'),
	require(ROOT_PATH. '/common/config/test-config.php'),
    require(ROOT_PATH. '/common/config/test-config-local.php'),

	require(ROOT_PATH . '/console/config/merge_config.php'),
	require(ROOT_PATH. '/console/config/test-config.php'),
    require(ROOT_PATH. '/console/config/test-config-local.php'),

	require(__DIR__ . '/../config/merge_config.php')
);
$config['id'] = 'lshop-test';
$config['basePath'] = YII_APP_BASE_PATH;
unset($config['components']['errorHandler']);
(new yii\console\Application($config));
