<?php
return [
    'email' => [
        'default_sender' => [
            'sender' => '',
            'sender_pwd' => '',
            'host' => '',
            'is_smtp' => 1,
            'smtp_auth' => 1,
            'smtp_secure' => 'ssl',
            'port' => 465,
            'content_charset' => 'utf-8'
        ]
    ],
    'worker' => [
        'email_worker_count' => 1,
    ],
    'amqp' => [
        'host' => '',
        'port' => '',
        'user' => '',
        'pwd' => '',
    ]
];
