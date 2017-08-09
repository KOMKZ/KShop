<?php
$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
return [
    'vendorPath' => ROOT_PATH . '/vendor',
    'timeZone' => 'Asia/Shanghai',
    'components' => [],
    'params' => $params
];
