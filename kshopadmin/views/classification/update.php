<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
?>
<div class="row">
    <?php $form = ActiveForm::begin([
        'action' => $routes['update_parent_action']
    ]);?>
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <div class="box-title">
                    基础信息
                </div>
                <div class="box-body">
                    <?php
                    echo $form->field($model, 'g_cls_name')->textInput();
                    echo $form->field($model, 'g_cls_show_name')->textInput();
                    ?>
                    <div class="form-group">
                        <label for=""><?= $model->getAttributeLabel('g_cls_pid')?></label>
                        <p class="form-control"><?= $model->g_cls_pid_label?></p>
                    </div>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', '修改'), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end();?>

    <?php $form = ActiveForm::begin([
        'action' => $routes['create_sub_action']
    ]);?>
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <div class="box-title">
                    添加子分类
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for=""><?= Yii::t('app', '父级分类名称')?></label>
                        <p class="form-control"><?= $model->g_cls_name?></p>
                    </div>
                    <?php
                    echo $form->field($newCls, 'g_cls_name')->textInput();
                    echo $form->field($newCls, 'g_cls_show_name')->textInput();
                    ?>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', '添加'), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end();?>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-body">
                <?php
                echo GridView::widget([
                    'caption' => sprintf('分类：%s 子分类列表', $model->g_cls_name),
                    'dataProvider' => $childClsProvider,
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
