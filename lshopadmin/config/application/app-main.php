<?php
$params = array_merge(
    require(__DIR__ . '/params.php')
);
return [
    'id' => 'lshop-admin',
    'defaultRoute' => 'site/index',
    'basePath' => dirname(dirname(__DIR__)),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'lshopadmin\controllers',
    'params' => $params,
    'components' => []
];
