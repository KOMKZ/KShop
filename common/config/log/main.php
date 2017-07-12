<?php
return [
    'log' => [
        'traceLevel' => 0,
        'targets' => [
            [
                'class' => 'yii\log\DbTarget',
                'logTable' => "{{%log_kshopapi}}",
                'levels' => ['error']
            ],
        ],
    ]
];
