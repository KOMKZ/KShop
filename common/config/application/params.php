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
    ],
    'jwt' => [
        'secret_key' => '',
        'allow_algs' => ['HS512'],
        'encode_alg' => 'HS512'
    ],
    'global_order_price_discount' => [
        [
            'class' => '\common\models\price\rules\OrderFullSliceRule',
            'fullValue' => 500000,
            'sliceValue' => 20000,
        ],
        [
            'class' => '\common\models\price\rules\OrderExemptExpressFee',
            'fullValue' => 50000
        ]
    ],
    'user_coupon_faker' => [
        ['oc_code' => 'CR000001', 'oc_begin' => time(), 'oc_end' => time() + 3600, 'oc_params' => ['full_value' => 500000, 'slice_value' => 50000]],
        ['oc_code' => 'CR000002', 'oc_begin' => time(), 'oc_end' => time() + 3600, 'oc_params' => ['full_value' => 100000, 'slice_value' => 200]],
    ],
    'transaction' => [
        'transaction_timeout' => 1800
    ]
];
