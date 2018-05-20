<?php
return [
	'urlManager' => [
		'enablePrettyUrl' => true,
		'showScriptName' => false,
		'rules' => [
			'OPTIONS <route:.*>' => "site/index",
			'trans_notification/<type:.*?>' => 'trans/notify',
			'POST /goods' => 'goods/create',
			'GET /goods/<g_code:.+>' => 'goods/view',
			'GET /goods' => 'goods/list',
			'POST /goods/<g_code:.+>/sku' => 'goods/create-sku',
		],
	]
];
