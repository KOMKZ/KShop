<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<?php
$form = ActiveForm::begin();
?>
<div class="row">
	<div class="col-md-6">
		<div class="box box-default">
			<div class="box-header with-header">
				<div class="box-title">
					基础信息
				</div>
				<div class="box-body">
					<?php
					echo $form->field($model, 'g_cls_name')->textInput();
					echo $form->field($model, 'g_cls_show_name')->textInput();
					?>
					<div class="form-group">
						<?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $form::end();?>
