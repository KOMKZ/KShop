<?php
use yii\bootstrap\Tabs;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\data\ArrayDataProvider;
use unclead\multipleinput\MultipleInput;
use yii\widgets\PjaxAsset;
use yii\widgets\Pjax;
use yii\helpers\Url;
// PjaxAsset::register($this);
$gid = $goods['g_id'];
$js = <<<JS
function form_bind_events(){
	$('#meta-form-box').on('click', 'form button[name="submit"]', function(){
		var form_selector = $('#save-g-meta-form');
		var is_old = form_selector.find("input[name='DynamicModel[gm_id]']").val();
		$.post(form_selector.attr('action'), form_selector.serialize(), function(res){
			refill_meta_form(res);
			if(!is_old){
				window.location.reload();
			}
		})
		return false;
	})
}
function grid_bind_events(){
	$('#meta-list-box').on('click', '.view-meta-btn', function(){
		var params = {
			gm_id : $(this).attr('data-id'),
			g_id : $(this).attr('data-gid')
		};
		refresh_g_metas_form(params);
	})
	$('#meta-list-box').on('click', '.delete-meta-btn', function(){
		$.post($(this).attr('href'), function(res){
			window.location.reload();
		})
		return false;
	})

}
function refresh_g_metas_form(params){
	$.get('/goods/pjax-save-g-metas' +
		   "?gm_id=" + params['gm_id'] + "&" +
		   "g_id=" + params['g_id'],
		   function(res){
		$('#meta-form-box').html(res);
	});
}
function refresh_g_metas_list(params){
	$.get('/goods/pjax-list-g-metas' +
		   "?" +
		   "g_id=" + params['g_id'],
		   function(res){
		$('#meta-list-box').html(res);
	});
}
function refill_meta_form(html){
	$('#meta-form-box').html(html);
}


form_bind_events();
grid_bind_events();
refresh_g_metas_form({"g_id" : "{$gid}"});
refresh_g_metas_list({"g_id" : "{$gid}"})
JS;
$this->registerJs($js);
$items = [];
?>


<?php
// 基础信息表单
ob_start();
$form = ActiveForm::begin();
?>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-md-4">
				<?php
				echo $form->field($goods, 'g_primary_name')->textInput();
				echo $form->field($goods, 'g_code')->textInput();
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<button class="btn btn-default">更新</button>
			</div>
		</div>
	</div>
</div>
<?php
ActiveForm::end();

$items[] = [
	'label' => '商品基础信息',
	'content' => ob_get_contents(),
	'active' => false
];
ob_end_clean();?>


<?php
// 详细信息表单
ob_start();
$form = ActiveForm::begin();
?>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-md-4">
				<?php
				echo $form->field($goodsDetail, 'g_intro_text')->textarea();
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<button class="btn btn-default">更新</button>
			</div>
		</div>
	</div>
</div>
<?php
ActiveForm::end();

$items[] = [
	'label' => '商品详细信息',
	'content' => ob_get_contents(),
];
ob_end_clean();
?>



<?php
// 详细信息表单
ob_start();
// $form = ActiveForm::begin();
?>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-md-8">
				<?php Pjax::begin(['enablePushState' => false]);?>
				<div id="meta-form-box">

				</div>
				<?php Pjax::end();?>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12">
				<?php Pjax::begin(['enablePushState' => false]);?>
				<div id="meta-list-box">

				</div>
				<?php Pjax::end();?>
			</div>
		</div>

	</div>
</div>
<?php
// ActiveForm::end();

$items[] = [
	'label' => '商品元属性信息',
	'content' => ob_get_contents(),
	'active' => true,
];
ob_end_clean();
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
		<?php
		echo Tabs::widget(['items' => $items]);
		?>
	</div>
</div>
