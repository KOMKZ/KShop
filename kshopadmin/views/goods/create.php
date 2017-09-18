<?php
use yii\bootstrap\ActiveForm;
use common\models\staticdata\ConstMap;
?>
<?php
$form = ActiveForm::begin();
?>
<div class="row">
	<div class="col-md-6">
		<div class="box box-default">
			<div class="box-header with-border">
				<div class="box-title">
					商品基础信息
				</div>
			</div>
			<div class="box-body">
				<?php
				echo $form->field($model, 'g_primary_name')->textInput();
				echo $form->field($model, 'g_secondary_name')->textInput();
				echo $form->field($model, 'g_status')->radioList(ConstMap::getConst('g_status'));
				?>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="box box-default">
			<div class="box-header with-border">
				<div class="box-title">
					商品详细信息
				</div>
			</div>
		</div>
	</div>
</div>
<?php
ActiveForm::end();
?>
