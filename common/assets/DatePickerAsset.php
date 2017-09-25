<?php
namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class DatePickerAsset extends AssetBundle
{
	public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins/datepicker';
	public $css = [
		'datepicker3.css',
	];
	public $js = [
		'bootstrap-datepicker.js'
	];
	public $depends = [
		'rmrevin\yii\fontawesome\AssetBundle',
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
		'yii\bootstrap\BootstrapPluginAsset',
	];
}
