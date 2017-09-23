<?php
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\CheckboxColumn;
use common\assets\BootstrapTreeAsset;

BootstrapTreeAsset::register($this);

$url = $routes['classification_result_action'];
$js = <<<JS
$.get("{$url}", function(res){
	var nodes = eval(res);
	if(nodes['code'] > 0){
		alert(nodes['message']);
	}else{
		$('#tree').treeview({
			data: nodes['data'],
			enableLinks: true,
			levels:1
		});
	}
})
var search = function(e) {
  var pattern = $('#input-search').val();
  var options = {
	ignoreCase: $('#chk-ignore-case').is(':checked'),
	exactMatch: $('#chk-exact-match').is(':checked'),
	revealResults: $('#chk-reveal-results').is(':checked')
  };
  var results = $('#tree').treeview('search', [ pattern, options ]);
  $('#tree').treeview('expandNode', [ results ,{}]);

  var output = '<p>' + results.length + ' matches found</p>';
  $.each(results, function (index, result) {
	output += '<p><a href="' + result.text + '">' + result.text + '</a></p>';
  });
  $('#search-output').html(output);
}
$('#btn-search').on('click', search);
$('#input-search').on('keyup', search);

$('#btn-clear-search').on('click', function (e) {
  $('#search-output').treeview('clearSearch');
  $('#input-search').val('');
  $('#search-output').html('');
});
JS;
$this->registerJs($js);
?>
<?php
echo Html::beginForm($routes['bulk_action'], 'post');
?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-default">
			<div class="box-header with-border">
				<div class="box-title">
					<?= Yii::t('app', "搜索分类")?>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="input-search" class="sr-only">Search Tree:</label>
								<input type="input" class="form-control" id="input-search" placeholder="Type to search..." value="">
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" class="checkbox" id="chk-ignore-case" value="false">
									Ignore Case
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" class="checkbox" id="chk-exact-match" value="false">
									Exact Match
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" class="checkbox" id="chk-reveal-results" value="false">
									Reveal Results
								</label>
							</div>
							<button type="button" class="btn btn-success" id="btn-search">Search</button>
							<button type="button" class="btn btn-default" id="btn-clear-search">Clear</button>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="box box-default">
			<div class="box-header with-header">
				<div class="box-title">
					<?= Yii::t('app', '所有分类')?>
				</div>
			</div>
			<div class="box-body">
				<div id="tree">

				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="box box-default">
			<div class="box-header with-header">
				<div class="box-title">
					<?= Yii::t('app', '搜索结果')?>
				</div>
			</div>
			<div class="box-body">
				<div id="search-output">

				</div>
			</div>
		</div>
	</div>
</div>


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
