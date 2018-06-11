<?php
use yii\bootstrap\Tabs;
use yii\bootstrap\ActiveForm;
use unclead\multipleinput\MultipleInput;

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
	</div>
</div>
<?php
ActiveForm::end();

$items[] = [
	'label' => '商品基础信息',
	'content' => ob_get_contents(),
	'active' => true
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
$form = ActiveForm::begin();
?>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-md-8">
				<?php
				echo $form->field($goods, 'g_metas')->widget(MultipleInput::className(), [
					'columns' => [
						[
							'name' => 'g_atr_id',
							'title' => '元属性id',
						],
						[
							'name' => 'gm_value',
							'title' => '元属性值',
						],
						[
							'name' => 'g_atr_name',
							'title' => '元属性名称',
						],
	
					]
				]);
				?>
			</div>
		</div>
	</div>
</div>
<?php
ActiveForm::end();

$items[] = [
	'label' => '商品元属性信息',
	'content' => ob_get_contents(),
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
