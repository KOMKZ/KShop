<?php
use common\models\sms\ar\Sms;
return [
    [
        'name' => 'user_register_succ',
        'candicates' => [
            Sms::PROVIDER_ALIDY => [
                'code' => 'SMS_105380037',
                'message' => "恭喜{name}成功注册为安全家会员{time}{}",
            ]
        ]
    ]
];
