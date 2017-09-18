<?php
use kshopadmin\assets\AppAsset;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

dmstr\web\AdminLteAsset::register($this);
$js = <<<JS
// $(document).on('pjax:success', function(xhr, data){
// 	var res = $.parseJSON(data);
//
// 	if(!res){
// 		$('#alert-box .alert-content').html("无法解析返回数据");
// 		$('#alert-box').show();
// 	}
//
// 	if(res['code'] > 0){
// 		$('#alert-box .alert-content').html('' + res['code'] + ',' + res['message']);
// 		$('#alert-box').show();
// 	}
// })
JS;
$this->registerJs($js);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?= Html::csrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
</head>
<body class="login-page">

<?php $this->beginBody() ?>
	<!-- <textarea style="display:none;" id="pjax-res"></textarea> -->
	<!-- <div class="alert alert-danger alert-dismissible text-center" id="alert-box" style="display:none;">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		<h4><i class="icon fa fa-ban"></i> Alert!</h4>
		<div class="alert-content">

		</div>
	</div> -->
	<?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
