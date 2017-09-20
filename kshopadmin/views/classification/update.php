<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\grid\CheckboxColumn;
use yii\widgets\Breadcrumbs;

$route = $routes['update_route'];
$clsPath = [];
$max = count($parents) - 1;
foreach($parents as $index => $cls){
	$route['id'] =  $cls->g_cls_id;
	if($max == $index){
		$clsPath[] = $cls->g_cls_name;
	}else{
		$clsPath[] = [
			'label' => $cls->g_cls_name,
			'url' => Url::to($route),
		];
	}
}
$clsPathHtml = Breadcrumbs::widget(['links' => $clsPath, 'homeLink' => false]);
?>
<?= $clsPathHtml;?>
<div class="row">
	<!-- 分类基础信息表单 -->
	<?php $form = ActiveForm::begin([
		'action' => $routes['update_parent_action']
	]);?>
	<div class="col-md-6">
		<div class="box box-default">
			<div class="box-header with-border">
				<div class="box-title">
					<p>基础信息</p>
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
						<?=
						Html::a('删除', $routes['delete_parent_action'], [
							'class' => 'btn btn-primary',
							'data-method' => 'post',
							'data-confirm' => Yii::t('app', '你确定要执行本次操作？')
						])
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php ActiveForm::end();?>

	<!-- 添加子分类表单 -->
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

<!-- 子类列表 -->
<?php
echo Html::beginForm($routes['bulk_action'], 'post');
?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-default">
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
			<div class="box-body">
				<?php
				echo GridView::widget([
					'caption' => sprintf('分类：%s 子分类列表', $model->g_cls_name),
					'dataProvider' => $childClsProvider,
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
