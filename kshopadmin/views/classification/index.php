<?php
use yii\grid\GridView;
use yii\helpers\Url;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-body">
                <?php
                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['attribute' => 'g_cls_id']
                        ,['attribute' => 'g_cls_name']
                        ,['attribute' => 'g_cls_show_name']
                        ,[
                            'attribute' => 'g_cls_created_at'
                            ,'format' => ['date', 'php:Y-m-d']
                        ]
                        ,[
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            // 'buttonOptions' => ['target' => '_blank'],
                            'template' => '{view}',
                            'urlCreator' => function ($action, $model, $key, $index) {
                                switch ($action) {
                                    case 'view':
                                    return Url::to(['classification/update', 'id' => $model->g_cls_id]);
                                }
                            },
                        ]
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
