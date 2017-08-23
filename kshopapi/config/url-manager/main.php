<?php
return [
    'urlManager' => [
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'rules' => [
            'trans_notification/<type:.*?>' => 'trans/notify',
        ],
    ]
];
