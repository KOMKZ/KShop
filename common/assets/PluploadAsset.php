<?php
namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class PluploadAsset extends AssetBundle
{
    public $sourcePath = "@bower/plupload";

    public $css = [
    ];
    public $js = [
        "js/plupload.full.min.js"
    ];
    public $depends = [
    ];
}
