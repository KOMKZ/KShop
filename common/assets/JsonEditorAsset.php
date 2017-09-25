<?php
namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class JsonEditorAsset extends AssetBundle
{
    public $sourcePath = "@bower/json-editor/dist";

    public $css = [
    ];
    public $js = [
        "jsoneditor.min.js"
    ];
    public $depends = [
    ];
}
