<?php
return [
	'urlManager' => [
		'enablePrettyUrl' => true,
		'showScriptName' => false,
		'rules' => [
			'OPTIONS <route:.*>' => "site/index",
			'trans_notification/<type:.*?>' => 'trans/notify',
			'file/output' => 'file/output',


			'GET <controller:[\w\-:]+>/<index:[^\/]+>/<sub:[\w\-:]+>/<sub_index:[^\/]+>/?' => "<controller>/view-<sub>",
			'PUT <controller:[\w\-:]+>/<index:[^\/]+>/<sub:[\w\-:]+>/<sub_index:[^\/]+>/?' => "<controller>/update-<sub>",
			'GET <controller:[\w\-:]+>/<index:[^\/]+>/?' => "<controller>/view",
			'GET <controller:[\w\-:]+>/?' => "<controller>/list",
			'GET <controller:[\w\-:]+>/<index:[^\/]+>/<sub:[\w\-:]+>/?' => "<controller>/list-<sub>",
			'POST <controller:[\w\-:]+>/<index:[^\/]+>/<sub:[\w\-:]+>/?' => '<controller>/create-<sub>',
			'POST <controller:[\w\-:]+>/?' => "<controller>/create",
			'PUT <controller:[\w\-:]+>/<index:[^\/]+>/?' => '<controller>/update'

		],
	]
];
