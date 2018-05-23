<?php
return [
    'fileoss' => [
        'class' => '\\common\models\\file\\drivers\\Oss',
        'access_key_id' => '',
        'access_secret_key' => '',
        'timeout' => 60,
        'bucket_cans' => [
            /*
            'pub_img' => [
                'bucket' => 'oss-45',
                'endpoint' => 'oss-cn-shenzhen.aliyuncs.com',
                'inner_endpoint' => 'oss-cn-shenzhen.aliyuncs.com',
                'is_cname' => false,
                'cdn' => true,
                'cdn_host' => 'http://test45.hsehome.org',
                'cdn_key' => '',
                'cdn_type' => 'n',
            ]
            */
        ],
    ]
];
