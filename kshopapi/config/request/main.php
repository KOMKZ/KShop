<?php
return [
    'request' => [
        'csrfParam' => '_csrf-frontend',
        'enableCookieValidation' => false,
        'parsers' => [
            'application/json' => 'yii\web\JsonParser',
        ]
    ]
];
