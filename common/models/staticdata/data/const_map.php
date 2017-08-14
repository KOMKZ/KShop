<?php

return [
    'u_status' => [
        \common\models\user\ar\User::STATUS_ACTIVE => Yii::t('app', '可用'),
        \common\models\user\ar\User::STATUS_NO_AUTH => Yii::t('app', "没有验证")
    ],
    'u_auth_status' => [
        \common\models\user\ar\User::NOT_AUTH => Yii::t('app', '未验证'),
        \common\models\user\ar\User::HAD_AUTH => Yii::t('app', "已验证")
    ],
    'file_save_type' => [
        \common\models\file\drivers\Disk::NAME => \Yii::t('app', '本地存储'),
        \common\models\file\drivers\Oss::NAME => \Yii::t('app', 'Oss存储'),
    ],
    'file_is_private' => [
        1 => \Yii::t('app', '私有访问'),
        0 => \Yii::t('app', '公有访问')
    ],
    'file_is_tmp' => [
        1 => \Yii::t('app', '临时文件'),
        0 => \Yii::t('app', '永久文件')
    ],
    'file_task_type' => [
        \common\models\file\ar\FileTask::TASK_CHUNK_UPLOAD => \Yii::t('app', "文件分片上传任务"),
    ],
    'file_task_status' => [
        \common\models\file\ar\FileTask::STATUS_INIT => \Yii::t('app', '初始化')
    ],
    'g_status' => [
        \common\models\goods\ar\Goods::STATUS_DRAFT => \Yii::t('app', "草稿"),
        \common\models\goods\ar\Goods::STATUS_ON_SALE => \Yii::t('app', "上架"),
        \common\models\goods\ar\Goods::STATUS_ON_NOT_SALE => \Yii::t('app', "下架"),
        \common\models\goods\ar\Goods::STATUS_FORBIDDEN => \Yii::t('app', "禁止销售"),
        \common\models\goods\ar\Goods::STATUS_DELETE => \Yii::t('app', "删除"),
    ],
    'g_atr_type' => [
        \common\models\goods\ar\GoodsAttr::ATR_TYPE_META => \Yii::t('app', '信息属性'),
        \common\models\goods\ar\GoodsAttr::ATR_TYPE_SKU => \Yii::t('app', 'sku属性'),
        \common\models\goods\ar\GoodsAttr::ATR_TYPE_OPTION => \Yii::t('app', 'sku属性')
    ],
    'g_atr_cls_type' => [
        \common\models\goods\ar\GoodsAttr::ATR_CLS_TYPE_CLS => Yii::t('app', "属性直属于分类"),
        \common\models\goods\ar\GoodsAttr::ATR_CLS_TYPE_GOODS => Yii::t('app', "属性直属于商品"),
    ],
    'g_sku_status' => [
        \common\models\goods\ar\GoodsSku::STATUS_ON_SALE => Yii::t('app', "上架"),
        \common\models\goods\ar\GoodsSku::STATUS_ON_NOT_SALE => Yii::t('app', "下架"),
        \common\models\goods\ar\GoodsSku::STATUS_INVALID => Yii::t('app', "失效"),
    ],
    'gs_type' => [
        \common\models\goods\ar\GoodsSource::TYPE_IMG => Yii::t('app', '图像')
    ],
    'gs_cls_type' => [
        \common\models\goods\ar\GoodsSource::CLS_TYPE_SKU => Yii::t('app', '商品sku资源'),
        \common\models\goods\ar\GoodsSource::CLS_TYPE_GOODS => Yii::t('app', '商品资源'),
        \common\models\goods\ar\GoodsSource::CLS_TYPE_OPTION => Yii::t('app', '商品属性值资源')
    ],
    'mail_content_type' => [
        \common\models\mail\ar\Mail::CONTENT_TYPE_HTML => Yii::t('app', 'text/html'),
    ],
    'mail_list_type' => [
        \common\models\mail\ar\Mail::LIST_TYPE_INLINE => Yii::t('app', '内联地址')
    ],
    'message_type' => [
        \common\models\message\Message::TYPE_ONE => Yii::t('app', '私信')
        ,\common\models\message\Message::TYPE_BOARD => Yii::t('app', '广播消息')
    ]
    ,'message_content_type' => [
        \common\models\message\Message::CONTENT_TYPE_PLAIN => Yii::t('app', '纯文本'),
        \common\models\message\Message::CONTENT_TYPE_TEMPLATE => Yii::t('app', '模板')
    ]
    ,'um_status' => [
        \common\models\user\ar\UserMessage::STATUS_UNREAD => Yii::t('app', '未读')
        ,\common\models\user\ar\UserMessage::STATUS_HAD_READ => Yii::t('app', '已读')
    ]
];
