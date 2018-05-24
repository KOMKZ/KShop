<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=lshop_test',
            'username' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
            'tablePrefix' => 'kshop_'
        ],
        'amqpConn' => new PhpAmqpLib\Connection\AMQPStreamConnection(
            'localhost',
            '5672',
            'guest',
            'guest'
        ),
        'filedisk' => [
            'class' => "\\common\models\\file\\drivers\\Disk",
            'base' => "",
            'host' => "",
            "urlRoute" => "",
            'dirMode' => 0777,
            'fileMode' => 0777,
        ],
        'fileoss' => [
            'class' => '\\common\models\\file\\drivers\\Oss',
            'access_key_id' => '',
            'access_secret_key' => '',
            'timeout' => 60,
            'bucket_cans' => [
                /*
                'pub_img' => [
                    'bucket' => '',
                    'endpoint' => '',
                    'inner_endpoint' => '',
                    'is_cname' => true,
                    'cdn' => true,
                    'cdn_host' => '',
                    'cdn_key' => '',
                    'cdn_type' => '',
                ]
                */
            ],
        ],
        'alipay' => [
    	    'class' => '\\common\models\\pay\\payment\\Alipay',
    	    'gatewayUrl' => 'https://openapi.alipaydev.com/gateway.do',
    	    'appId' => '',
    	    'rsaPrivateKeyFilePath' => '',
    	    'alipayrsaPublicKey' => '',
    	    'notifyUrl' => '',
    	    'returnUrl' => '',
    	],
        'wxpay' => [
            'class' => '\\common\models\\pay\\payment\\Wxpay',
            'appid' => '',
            'mchid' => '',
            'key' => '',
            'appsecret' => '',
            'sslcert_path' => '',
            'sslkey_path' => '',
            // 'notifyUrl' => 'http://demo.com:18064/pay/notify/wxpay'
            'notifyUrl' => ''
        ]
    ],
    'params' => [
        'email' => [
    		'default_sender' => [
    			'sender' => '',
    			'sender_pwd' => '',
    			'host' => 'smtp.qq.com',
    			'is_smtp' => 1,
    			'smtp_auth' => 1,
    			'smtp_secure' => 'ssl',
    			'port' => 465,
    			'content_charset' => 'utf-8'
    		]
    	],
    ]
];
