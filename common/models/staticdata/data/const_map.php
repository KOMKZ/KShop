<?php

return [
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
    ]
];
