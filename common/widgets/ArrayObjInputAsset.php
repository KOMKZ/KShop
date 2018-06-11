<?php
/**
 * Author: Eugine Terentev <eugine@terentev.net>
 */
namespace common\widgets;
use yii\web\AssetBundle;

class ArrayObjInputAsset extends AssetBundle{
    public function init(){
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }
    public $js = [
        'js/array-obj-input.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset'
    ];
}
