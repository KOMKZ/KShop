<?php
$params = array_merge(
    require(dirname(__DIR__) . '/../../common/config/application/params.php'),
    require(__DIR__ . '/params.php')
);
return [
    'id' => 'kshopadmin',
    'defaultRoute' => 'site/index',
    'basePath' => dirname(dirname(__DIR__)),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'kshopadmin\controllers',
    'params' => $params,
    'components' => []
];
