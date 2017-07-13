<?php
use Yii;
return [
    'file_save_type' => [
        \common\models\file\drivers\Disk::NAME => Yii::t('app', '本地存储')
    ]
];
