<?php
$params = array_merge(
    require(dirname(__DIR__) . '/../../common/config/application/params.php'),
    require(__DIR__ . '/params.php')
);
return [
    'id' => 'kshopapi',
    'defaultRoute' => 'api/index',
    'basePath' => dirname(dirname(__DIR__)),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'kshopapi\controllers',
    'params' => $params,
    'components' => []
];
