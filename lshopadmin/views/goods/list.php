<?php
use yii\helpers\Url;
use yii\grid\GridView;
?>
<div class="row">
	<div class="col-md-12">
		<div class="box">
			<div class="box-body">

			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="box">
			<div class="box-body">
				<?php
		        echo GridView::widget([
		            'dataProvider' => $dataProvider,
		            'columns' => [
		                [ 'attribute' => 'g_id'],
						[ 'attribute' => 'g_primary_name'],
						[ 'attribute' => 'g_status'],
						[ 'attribute' => 'g_created_at', 'format' => ['datetime', 'php:Y-m-d H:i:s']],
						[ 'attribute' => 'g_updated_at', 'format' => ['datetime', 'php:Y-m-d H:i:s']],
		                [
		                    'class' => 'yii\grid\ActionColumn',
		                    'template' => '{update}',
		                    'header' => '操作',
		                    'visibleButtons' => [
		                        'view' => function ($model, $key, $index) {
		                            return $model['od_can_look_appeal'];
		                        }
		                    ],
		                    'urlCreator' => function ($action, $model, $key, $index) {
		                        return Url::to(['goods/update', 'g_id' => $model['g_id']]);
		                    },
		                    'buttonOptions' => ['target' => '_blank'],
		                ],
		            ]
		        ]);
		        ?>
			</div>
		</div>
	</div>
</div>
