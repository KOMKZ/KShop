<?php
use Yii;
use yii\helpers\Html;
use kshopadmin\assets\AppAsset;
AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>KShop Admin System</title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php echo $content;?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
