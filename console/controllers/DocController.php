<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 * @def #global_res
 * - code integer,错误编号
 * - message string,错误信息
 * - data mixed,返回信息
 *
 * @def #goods_item
 * - g_primary_name string,商品主要名称
 * - g_detail_info object#g_detail_info,商品详细信息
 *
 * @def #g_detail_info
 * - g_long_text string,商品详细介绍
 * - g_middle_text string,商品中长介绍
 */
class DocController extends Controller
{
    public $save_dir = "/tmp";

    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['save_dir']
        );
    }
    /**
     * [init description]
     * @return [type] [description]
     */
    public function init(){
        parent::init();
        foreach($this->getAlias() as $alias => $path){
            Yii::setAlias($alias, $path);
        }
    }
    /**
     * @api get,goods/list,获取商品列表
     * - g_id optional,integer,in_query,商品id
     * - g_create_uid optional,integer,in_query,商品创建者
     * - g_status optional,string,in_query,is_enum(draft:草稿|online:上架),商品状态
     * @return #global_res,返回商品列表
     * - data array#goods_item,商品列表
     */
    public function actionGeneApi($module){
        $files = $this->getFilesFromModule($module);
        $files = $this->prepareFiles($files);
        $phpDoces = [];
        foreach($files as $file){
            $phpDoces[] = $this->parseDocesFormPhpFile($file);
        }
        console($phpDoces);
    }

    protected function geneSwgRefFileFromDef(){

    }

    protected function geneSwgApiFileFromDef(){

    }

    protected function geneSwgRootFileFromDef(){

    }

    

    protected function parseDocesFormPhpFile($file){
        $content = file_get_contents($file);
        $result = preg_match_all("/\/\*[\s\S]*?\*\//", $content, $matches);
        if(!$result){
            return [];
        }
        $defs = [];
        $apis = [];
        foreach($matches[0] as $docBlock){
            $type = $this->getDocContentType($docBlock);
            switch ($type) {
                case 'def':
                    foreach($this->parseDefFromDocBlock($docBlock) as $def){
                        $defs[$def['name']] = $def;
                    }
                    break;
                case 'api':
                    $apis[] = $this->parseApiFormDocBlock($docBlock);
                    break;
                default:
                    break;
            }
        }
        console($defs, $apis);

    }
    protected function parseApiFormDocBlock($block){
        $method = "";
        $path  = "";
        $des = '';
        $params = [];
        $return = ['ref' => null, 'des' => '', 'props' => []];
        $result = preg_match(sprintf("/%s%s%s%s%s/u",
        "@api\s+(?P<method>(get|post|put|patch)),\s*",
        "(?P<path>[\s\S]+?),\s*(?P<des>[\S\s]+?)\n+",
        "(?P<props>[\s\S]*)\n*\s*\*\s*",
        "@return\s+(?P<return_def>(#[\s\S]+?))\n+",
        "(?P<return_props>[^\n]*)\n*\s*"
        ), $block, $matches);
        if(!$result){
            throw new \Exception("api定义语法错误");
        }
        $method = $matches['method'];
        $path = $matches['path'];
        $des = $matches['des'];
        $params = $this->parsePropsFromDocBlock($matches['props']);
        list($returnRef, $returnDes) = $this->parseReturnFromDocBlock($matches['return_def']);
        $return['ref'] = $returnRef;
        $return['des'] = $returnDes;
        $return['props'] = $this->parsePropsFromDocBlock($matches['return_props']);
        return [
            'method' => $method,
            'path' => $path,
            'des' => $des,
            'params' => $params,
            'return' => $return
        ];
    }
    protected function parseReturnFromDocBlock($docBlock){
        $def = preg_split('/\s*,\s*/', $docBlock, 2, PREG_SPLIT_NO_EMPTY);
        $def[0] = ltrim($def[0], '#');
        return $def;
    }
    protected function parseDefFromDocBlock($block){
        $defs = [];
        $result = preg_match_all('/\*\s*@def\s*#([a-zA-Z0-9\-\_\s]+)\n+\s*\*\s*([\s\S]*?)\n+\s*\*\s*[\n\/]/', $block, $matches);
        if(!$result){
            throw new \Exception("解析def出错");
        }
        foreach($matches[1] as $index => $defName){
            $props  = $this->parsePropsFromDocBlock($matches[2][$index]);
            $defs[] = [
                'name' => $defName,
                'props' => $props
            ];
        }
        return $defs;
    }
    protected function parsePropsFromDocBlock($propsDefs){
        $props = explode("\n", $propsDefs);
        foreach($props as $index => $propDef){
            $propDef = trim($propDef, "\n*- ");
            if(!$propDef){
                unset($props[$index]);
                continue;
            }
            $result = preg_match("/(?P<name>[a-z0-9A-Z\_\-]+)\s+(?P<prop_def>[^\n]+)/", $propDef, $vars);
            if(!$result){
                throw new \Exception("def的语法书写错误 " . $propDef);
            }
            $def = [
                'name' => $vars['name'],
                'type' => null,
                'ref' => null,
                'des' => null,
                'required' => null,
                'enum' => null,
                'query_or_path' => null
            ];
            $propdefs = preg_split('/\s*,\s*/', $vars['prop_def'], 2, PREG_SPLIT_NO_EMPTY);
            if($this->isRequiredProp($propdefs[0])){
                $def['required'] = $propdefs[0];
                $propdefs = preg_split('/\s*,\s*/', $propdefs[1], 2, PREG_SPLIT_NO_EMPTY);
            }
            if($this->isTypeProp($propdefs[0])){
                list($type, $ref) = $this->getRealType($propdefs[0]);
                if(!$type){
                    throw new \Exception(sprintf("def prop类型%s无效", $propdefs[0]));
                }
                $def['type'] = $type;
                $def['ref'] = $ref;
                $propdefs = preg_split('/\s*,\s*/', $propdefs[1], 2, PREG_SPLIT_NO_EMPTY);
            }
            if($this->isPathOrQueryProp($propdefs[0])){
                $def['query_or_path'] = $propdefs[0];
                $propdefs = preg_split('/\s*,\s*/', $propdefs[1], 2, PREG_SPLIT_NO_EMPTY);
            }
            if($this->isEnumProp($propdefs[0])){
                $def['enum'] = $propdefs[0];
                $propdefs = preg_split('/\s*,\s*/', $propdefs[1], 2, PREG_SPLIT_NO_EMPTY);
                $def['des'] = $propdefs[0];
            }else{
                $def['des'] = $propdefs[0];
            }
            $props[$index] = $def;
        }
        return $props;
    }
    protected function isEnumProp($str){
        return preg_match("/is_enum\([\s\S]+?\)/", $str);
    }
    protected function isRequiredProp($str){
        return in_array($str, ['optional', 'required']);
    }
    protected function isPathOrQueryProp($str){
        return in_array($str, ['in_query', 'in_path']);
    }
    protected function isTypeProp($str){
        $types = ['integer', 'boolean', 'string', 'mixed'];
        if(in_array($str, $types)){
            return true;
        }
        if(preg_match('/(object|array)#([a-zA-Z0-9\_]+)/', $str, $matches)){
            return true;
        }else{
            return false;
        }
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
            return [];
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
            '@hsefr' => '/home/master/pro/php/hsehome2.0/app/frontend',
            '@kshopapi' => '/home/kitralzhong/pro/php/kshop/kshopapi',
            '@homekscmd' => '/home/kitralzhong/pro/php/kshop/console',
            '@cpykscmd' => '/home/master/pro/php/kshop/console',
        ];
    }
    protected function getFilesFromModule($module){
        return ArrayHelper::getValue(Yii::$app->params['apifiles'], $module, []);
    }
}
