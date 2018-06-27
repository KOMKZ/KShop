<?php
use yii\bootstrap\ActiveForm;
use dmstr\widgets\Alert;

$form = ActiveForm::begin([
	'id' => 'save-g-meta-form'
]);

?>

<div class="row">
	<div class="col-md-5">
		<?= Alert::widget() ?>
		<?php
		echo $form->field($model, 'gm_id')->textInput();
		echo $form->field($model, 'g_atr_id')->textInput();
		echo $form->field($model, 'g_atr_name')->textInput();
		echo $form->field($model, 'gm_value')->textarea();
		?>
	</div>
	<div class="col-md-5">

	</div>
</div>
<div class="row">
	<div class="col-md-5">
		<div class="from-group">
			<button class="btn btn-default" name="submit" >保存</button>
		</div>
	</div>
</div>
<?php
ActiveForm::end();
?>
