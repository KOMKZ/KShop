<?php
return [
	'urlManager' => [
		'enablePrettyUrl' => true,
		'showScriptName' => false,
		'rules' => [
			'OPTIONS <route:.*>' => "api/index",
			'trans_notification/<type:.*?>' => 'trans/notify',
		],
	]
];
