<?php
return [
	'urlManager' => [
		'enablePrettyUrl' => true,
		'showScriptName' => false,
		'rules' => [
			'OPTIONS <route:.*>' => "site/index",
			'trans_notification/<type:.*?>' => 'trans/notify',


			'GET <controller:[\w\-]+>/<index:.+>/?' => "<controller>/view",
			'GET <controller:[\w\-]+>/?' => "<controller>/list",
			'POST <controller:[\w\-]+>/?' => "<controller>/create",
			'POST <controller:[\w\-]+>/<index:.+>/<sub:[\w\-]+>/?' => '<controller>/create-<sub>',
			'PUT <controller:[\w\-]+>/<index:.+>/?' => '<controller>/update'

		],
	]
];
