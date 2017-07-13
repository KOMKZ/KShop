<?php
use Yii;
return [
    'file_save_type' => [
        \common\models\file\drivers\Disk::NAME => Yii::t('app', '本地存储')
    ],
    'file_is_private' => [
        1 => Yii::t('app', '私有访问'),
        0 => Yii::t('app', '公有访问')
    ],
    'file_is_tmp' => [
        1 => Yii::t('app', '临时文件'),
        0 => Yii::t('app', '永久文件')
    ]
];
