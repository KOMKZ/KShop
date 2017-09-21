<?php
namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class BootstrapTreeAsset extends AssetBundle
{
	public $sourcePath = "@bower/bootstrap-treeview/src";

	public $css = [
		'css/bootstrap-treeview.css'
	];
	public $js = [
		"js/bootstrap-treeview.js"
	];
	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
	];
}
