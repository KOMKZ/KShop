<?php
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\CheckboxColumn;
?>
<?php
echo Html::beginForm($routes['bulk_action'], 'post');
?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-default">
			<div class="box-body">
				<div class="box-body">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label for=""><?= Yii::t('app', '选择操作')?></label>
								<?php
								echo Html::dropDownList('action', '',
								[
									'' => Yii::t('app', '没有选择')
									,'bulk_delete_action' => Yii::t('app', '批量删除')
								],
								['class'=>'form-control']
								);
								?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<?php
								echo Html::submitButton(Yii::t('app', "提交"), [
									'class' => 'btn btn-default',
									'data-confirm' => Yii::t('app', '你确定要执行批量操作吗？')
								]);
								?>
							</div>
						</div>
					</div>
				</div>
				<?php
				echo GridView::widget([
					'dataProvider' => $dataProvider,
					'columns' => [
						['class' => CheckboxColumn::className()]
						,['attribute' => 'g_cls_id']
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
							'template' => '{view} {delete}',
							'urlCreator' => function ($action, $model, $key, $index) use($routes){
								switch ($action) {
									case 'view':
										 $route = $routes['update_route'];
										 $route['id'] =  $model->g_cls_id;
										 return Url::to($route);
									case "delete":
										$route = $routes['delete_route'];
										$route['id'] =  $model->g_cls_id;
										return Url::to($route);
								}
							},
							'buttons' => [
								'delete' => function($url, $model, $key){
									return Html::a(Yii::t('app', '删除'), $url, [
										'class' => 'btn btn-primary btn-xs agree-wa-btn',
										'data-method' => 'post',
										'data-confirm' => Yii::t('app', '是否确认删除')
									]);
								},
								'view' => function($url, $model, $key){
									return Html::a(Yii::t('app', '查看'), $url, [
										'class' => 'btn btn-primary btn-xs agree-wa-btn'
									]);
								},
							],
						]
					]
				]);
				?>
			</div>
		</div>
	</div>
</div>
<?php
echo Html::endForm();
?>
