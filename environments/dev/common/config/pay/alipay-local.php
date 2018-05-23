<?php
return [
	// 沙箱环境
	'alipay' => [
	    'class' => '\\common\models\\pay\\payment\\Alipay',
	    'gatewayUrl' => 'https://openapi.alipaydev.com/gateway.do',
	    'appId' => '2016101000649447',
	    'rsaPrivateKeyFilePath' => '/home/master/data/alipay_demo/app_private_key.pem',
	    'alipayrsaPublicKey' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuMICWB3V1EPq7wveUC9LjzXszmwp+nKEM5jg06MV+3yYOZqgxa6sahCg2LrOSJDQ7uD9fq47Z0dk9uTlEOn2QJyR9jLcPUdrdHwFJSkQAxNqZFwcIwphZ6/BIpZLR07K8rNi3GjvDZ/1RQ2WpZbiL7iwrsVH+4ysRRreXGJhzbP2+JraNjx2OnHphIOnkILBe/P0b50nJe2XmEDTCQo2ECZVJoBwovwXAKAk4c8GbvuSg3JgSG9stxN50xojn95H7Czs+PKRBwPsXKyJrvgycjtznHkCLtd+/fouqroBmzzPS217htyIrC7Cq9AeD24Bpkzb4vFvQFfslOnJVQKGMQIDAQAB',
	    'notifyUrl' => 'http://trainor.myds.me:18064/pay/notify/alipay_notify',
	    'returnUrl' => '',
	],
];
