<?php
return [
    'log' => [
        'traceLevel' => 0,
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error']
            ],
        ],
    ]
];
