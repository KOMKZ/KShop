<?php
return [
	'urlManager' => [
		'enablePrettyUrl' => true,
		'showScriptName' => false,
		'rules' => [
			'OPTIONS <route:.*>' => "site/index",
			'trans_notification/<type:.*?>' => 'trans/notify',
		],
	]
];
