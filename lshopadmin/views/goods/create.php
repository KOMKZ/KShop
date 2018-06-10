<?php
use yii\bootstrap\ActiveForm;
$form = ActiveForm::begin();
?>
<div class="row">
	<div class="col-md-12">
		<div class="box">
			<div class="box-body">
				<input type="submit" name="" value="创建" class="btn btn-default">
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-4">
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title">商品基础信息</h3>
			</div>
			<div class="box-body">
				<?php
				echo $form->field($model, 'g_primary_name')->textInput();
				echo $form->field($model, 'g_code')->textInput();
				echo $form->field($model, 'g_intro_text')->textarea();
				?>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title">商品分类信息</h3>
			</div>
			<div class="box-body">
				<?php
				echo $form->field($model, 'g_cls_id')->textInput();
				?>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title">商品元属性信息</h3>
			</div>
			<div class="box-body">
				<?php
				echo $form->field($model, 'g_metas')->arrayObjInput([
					'g_atr_id' => ['label' => '元属性id', 'inputType' => 'textInput'],
					'gm_value' => ['label' => '元属性值', 'inputType' => 'textInput']
				]);
				?>
			</div>
		</div>
	</div>
</div>
<?php ActiveForm::end();?>
