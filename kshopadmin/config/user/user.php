<?php
return [
    'user' => [
        'class' => 'yii\web\User',
        'identityClass' => '\common\models\user\ar\User',
        'enableSession' => true,
        'loginUrl' => ['site/login'],
    ],
];
