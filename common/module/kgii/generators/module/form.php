<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator common\module\kgii\generators\module\Generator */

?>
<div class="module-form">
<?php
    echo $form->field($generator, 'moduleClass');
    echo $form->field($generator, 'moduleID');
?>
</div>
