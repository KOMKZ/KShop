<?php
return [
    'log' => [
        'traceLevel' => 0,
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error'],
                'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'],
                'fileMode' => 0777
            ],
        ],
    ]
];
