<?php
namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class DateTimePickerAsset extends AssetBundle
{
	public $sourcePath = '@bower/smalot-bootstrap-datetimepicker';
	public $css = [
		'css/bootstrap-datetimepicker.min.css',
	];
	public $js = [
		'js/bootstrap-datetimepicker.min.js'
	];
	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
		'yii\bootstrap\BootstrapPluginAsset',
	];
}
