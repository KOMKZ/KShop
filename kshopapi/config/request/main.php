<?php
return [
    'request' => [
        'csrfParam' => '_csrf-frontend',
        'parsers' => [
            'application/json' => 'yii\web\JsonParser',
        ]
    ]
];
