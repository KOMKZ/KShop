<?php
$params = array_merge(
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
