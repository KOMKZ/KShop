<?php
return [
    'request' => [
        'enableCookieValidation' => false,
        'csrfParam' => '_csrf-frontend',
        'parsers' => [
            'application/json' => 'yii\web\JsonParser',
        ]
    ]
];
