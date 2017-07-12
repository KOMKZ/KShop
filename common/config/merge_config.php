<?php
$configDir = dirname(__FILE__);
$config = \yii\helpers\ArrayHelper::merge(
    require($configDir .  '/application/app-main.php'),
    []
);
$localConfig = [];
foreach(\yii\helpers\FileHelper::findFiles($configDir, [
    'filter' => function($path){
        if(!is_file($path)){
            return true;
        }
        $type = basename(dirname($path));
        return 'application' != $type && !in_array(basename($path), [
            '.gitignore',
            'bootstrap.php',
            'merge_config.php'
        ]);
    }
]) as $configFile){
    if(preg_match('/.+\-local.php$/', $configFile)){
        $localConfig[] = $configFile;
        continue;
    }
    $config['components'] = \yii\helpers\ArrayHelper::merge($config['components'], require($configFile));
}
foreach($localConfig as $configFile){
    $config['components'] = \yii\helpers\ArrayHelper::merge($config['components'], require($configFile));
}
return $config;
