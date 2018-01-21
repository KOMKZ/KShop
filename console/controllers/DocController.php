<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;

/**
 *
 */
class DocController extends Controller
{
    public function init(){
        parent::init();
        foreach($this->getAlias() as $alias => $path){
            Yii::setAlias($alias, $path);
        }
    }

    public function actionGene(){
        $files = func_get_args();
        $files = $this->prepareFiles($files);
        $phpDoces = [];
        foreach($files as $file){
            $phpDoces[] = $this->parseDocesFormPhpFile($file);
        }
        console($phpDoces);
    }
    protected function parseDocesFormPhpFile($file){
        $content = file_get_contents($file);
        $result = preg_match_all("/\/\*[\s\S]*?\*\//", $content, $matches);
        if(!$result){
            return [];
        }
        foreach($matches[0] as $docBlock){
            $type = $this->getDocContentType($docBlock);
            $parseResult = [];
            switch ($type) {
                case 'def':
                    $parseResult = $this->parseDefFromDocBlock($docBlock);
                    break;
                case 'api':
                    break;
                default:
                    break;
            }
            console($docBlock, $parseResult);
        }
        console(1);
    }
    protected function parseDefFromDocBlock($block){
        $defs = [];
        $result = preg_match_all('/\*\s*@def\s*#([a-zA-Z0-9\-\_\s]+)\n+\s*\*\s*([\s\S]*?)\n+\s*\*\s*[\n\/]/', $block, $matches);
        if(!$result){
            throw new \Exception("解析def出错");
        }
        foreach($matches[1] as $index => $defName){
            $props  = $this->parsePropsFromDocBlock($matches[2][$index]);
        }
        console($props);
        return $matches;
    }
    protected function parsePropsFromDocBlock($propsDefs){
        $props = explode("\n", $propsDefs);
        foreach($props as $index => $propDef){
            $propDef = trim($propDef, "\n\s*- ");
            if(!$propDef){
                continue;
            }
            $result = preg_match("/(?P<name>[a-z0-9A-Z\_\-]+)\s+(?P<prop_def>[^\n]+)/", $propDef, $vars);
            if(!$result){
                throw new \Exception("def的语法书写错误 " . $propDef);
            }
            $propdefs = preg_split('/\s*,\s*/', $vars['prop_def'], 2, PREG_SPLIT_NO_EMPTY);
            list($type, $def) = $this->getRealType($propdefs['0']);
            if(!$type){
                throw new \Exception("def prop类型无效");
            }
            $props[$index] = [
                'name' => $vars['name'],
                'type' => $type,
                'ref' => $def,
                'des' => $propdefs[1] ? $propdefs[1] : ''
            ];
        }
        return $props;
    }
    protected function getRealType($type){
        $types = ['integer', 'boolean', 'string', 'mixed'];
        if(in_array($type, $types)){
            return [$type, null];
        }
        if(preg_match('/(object|array)#([a-zA-Z0-9\_]+)/', $type, $matches)){
            return [$matches[1], $matches[2]];
        }else{
            return [false, false];
        }
    }
    protected function getDocContentType($content){
        if(preg_match_all("/\* @def/", $content, $matches)){
            return "def";
        }elseif(preg_match_all("/\* @api/", $content, $matches)){
            return "api";
        }else{
            return false;
        }
    }
    protected function prepareFiles($files = []){
        if(!$files){
            return false;
        }
        foreach($files as $index => $file){
            $path = Yii::getAlias($file);
            if(!file_exists($path)){
                throw new \Exception(sprintf("文件不存在%s", $file));
                return false;
            }
            $files[$index] = Yii::getAlias($file);

        }
        return $files;
    }
    protected function getAlias(){
        return [
            '@hsefr' => '/home/master/pro/php/hsehome2.0/app/frontend'
        ];
    }
}
