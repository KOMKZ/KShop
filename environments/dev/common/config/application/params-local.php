<?php
return [
	'email' => [
		'default_sender' => [
			'sender' => 'kitral.zhong@trainor.cn',
			'sender_pwd' => 'TDSZ2016kz',
			'host' => 'smtp.qq.com',
			'is_smtp' => 1,
			'smtp_auth' => 1,
			'smtp_secure' => 'ssl',
			'port' => 465,
			'content_charset' => 'utf-8'
		]
	],
	'worker' => [
		'email_worker_count' => 2
	],
	'amqp' => [
		'host' => 'localhost',
		'port' => '5672',
		'user' => 'guest',
		'pwd' => 'guest',
	],
	'jwt' => [
		'secret_key' => 'abc',
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
		['oc_code' => 'CR000002', 'oc_begin' => time(), 'oc_end' => time() + 3600, 'oc_params' => ['full_value' => 100000, 'slice_value' => 20000]],
	],
	'wxpay_notification' => "<xml><appid><![CDATA[wxeaac8f306ad9d7c3]]></appid>
<bank_type><![CDATA[CFT]]></bank_type>
<cash_fee><![CDATA[1]]></cash_fee>
<fee_type><![CDATA[CNY]]></fee_type>
<is_subscribe><![CDATA[Y]]></is_subscribe>
<mch_id><![CDATA[1394864202]]></mch_id>
<nonce_str><![CDATA[fyqvsjgqcgch0eywqctv4i25rttr0bcv]]></nonce_str>
<openid><![CDATA[o5fYBxMPJXVxEEWcI2INojRWrLC4]]></openid>
<out_trade_no><![CDATA[TR142017173224081818]]></out_trade_no>
<result_code><![CDATA[SUCCESS]]></result_code>
<return_code><![CDATA[SUCCESS]]></return_code>
<sign><![CDATA[B20395C5A407831F6040122E59BF41B2]]></sign>
<time_end><![CDATA[20170824141947]]></time_end>
<total_fee>1</total_fee>
<trade_type><![CDATA[NATIVE]]></trade_type>
<transaction_id><![CDATA[4007132001201708247980614361]]></transaction_id>
</xml>"
];
