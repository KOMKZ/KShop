<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;
use yii\console\controllers\MigrateController;
use yii\helpers\ArrayHelper;


class RbacController extends Controller{
	protected $db = null;
	public $api_base = "@kshopapi/controllers";
	public $api_namespace = "\kshopapi\controllers";
	public $rbac_def_file = "@app/rbac/api-rbac-def-data.php";
	public $rbac_all_yaml_file = "@app/rbac/api-rbac-all-data.yaml";

	public function actionInstallRbacData(){
		// 将yaml文件导入进来
	}
	/**
	 * 生成rbac数据
	 * @return [type] [description]
	 */
	public function actionGeneRbacData(){
		// 初始化数据环境
		// $hasTable = Yii::$app->db->createCommand("select * from {{%migration}} where version = 'm140506_102106_rbac_init'")->queryAll();
		// if($hasTable){
		// 	$this->removeRbacData();
		// }else{
		// 	system(sprintf("%s/yii migrate/up -p=@app/rbac --interactive=0", getcwd()));
		// }

		// 构造权限数据
		$exclude = [
			'api/error' => null
		];
		$permissionsFromApi = $this->parsePermFromApi($exclude);
		$permissionsFromDef = $this->getPermFromDef();
		$permissions = ArrayHelper::merge($permissionsFromApi, $permissionsFromDef);
		// 构造角色数据
		$roles = $this->getRoleFromDef();
		// 构造权限分配
		$assign = $this->getAssignFromDef();
		$oldData = ['roles' => [], 'permissions' => [], 'assign' => []];
		if(file_exists(Yii::getAlias($this->rbac_all_yaml_file))){
			$data = spyc_load_file(Yii::getAlias($this->rbac_all_yaml_file));
			foreach($data['roles'] as $index => $item){
				$item = explode('@', $item);
				$oldData['roles'][$item[0]] = $item;
			}
			foreach($data['permissions'] as $index => $item){
				$item = explode('@', $item);
				$oldData['permissions'][$item[0]] = $item;
			}
			foreach($data['assign'] as $index => $item){
				$item = explode('@', $item);
				$oldData['assign'][$item[0] . '-' . $item[1]] = $item;
			}
		}
		$rbacResult = array_merge($oldData, [
			'roles' => $roles,
			'permissions' => $permissions,
			'assign' => $assign
		]);
		$rbacData = [
			'roles' => [],
			'permissions' => [],
			'assign' => [],
		];
		foreach($rbacResult['roles'] as $item){
			$rbacData['roles'][] = implode('@', $item);
		}
		foreach($rbacResult['permissions'] as $item){
			$rbacData['permissions'][] = implode('@', $item);
		}
		foreach($rbacResult['assign'] as $item){
			$rbacData['assign'][] = implode('@', $item);
		}
		file_put_contents(Yii::getAlias($this->rbac_all_yaml_file), spyc_dump($rbacData));
	}
	protected function getAssignFromDef(){
		$defs = require(Yii::getAlias($this->rbac_def_file));
		$assign = [];
		foreach(ArrayHelper::getValue($defs, 'assign', []) as $item){
			$assign[$item[0] . '-' . $item[1]] = $item;
		}
		return $assign;
	}
	protected function getPermFromDef(){
		$defs = require(Yii::getAlias($this->rbac_def_file));
		$perms = ArrayHelper::getValue($defs, 'permissions', []);
		return ArrayHelper::index($perms, 0);
	}
	protected function getRoleFromDef(){
		$defs = require(Yii::getAlias($this->rbac_def_file));
		$perms = ArrayHelper::getValue($defs, 'roles', []);
		return ArrayHelper::index($perms, 0);
	}
	protected function parsePermFromApi($exclude = []){
		$apiDir = Yii::getAlias($this->api_base);
		$apis = [];
		foreach(glob($apiDir . '/*') as $item){
			 $controllerName = preg_replace('/Controller.php/', '', basename($item));
			 $controllerId = strtolower(trim(preg_replace('/([A-Z][a-z0-9]*)/', "$1-", $controllerName), '-'));
			 $controllerClass = $this->api_namespace . "\\" . $controllerName;
			 $reflection = new \ReflectionClass($this->api_namespace . '\\' . $controllerName . 'Controller');
			 foreach($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method){
				 if(preg_match('/action([A-Z][a-z0-9A-Z]*)/', $method->getName(), $matches)){
					 $actionId = strtolower(trim(preg_replace('/([A-Z][a-z0-9]*)/', "$1-", $matches[1]), '-'));
					 $api = sprintf("%s/%s", $controllerId, $actionId);
					 $des = "";
					 $docblock = $method->getDocComment();
					 if($docblock){
						 if(preg_match('/@api\s+[^\n]+,\s*([\s\S]+?)[\n\s]+/u', $docblock, $matches)){
							 $des = $matches[1];
						 }
					 }
					 if(!array_key_exists($api, $exclude)){
						 $apis[$api] = [$api, $des];
					 }
				 }
			 }
		}
		return $apis;
	}
	protected function removeRbacData(){
		$authManager = $this->getAuthManager();
		$this->db = $authManager->db;
		$this->dropTable($authManager->assignmentTable);
		$this->dropTable($authManager->itemChildTable);
		$this->dropTable($authManager->itemTable);
		$this->dropTable($authManager->ruleTable);
		$this->delete("{{%migration}}", "version = 'm140506_102106_rbac_init'");
	}
	protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

	public function dropTable($table)
	{
		echo "    > drop table $table ...";
		$time = microtime(true);
		$this->db->createCommand()->dropTable($table)->execute();
		echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
	}
	public function delete($table, $condition = '', $params = [])
    {
        echo "    > delete from $table ...";
        $time = microtime(true);
        $this->db->createCommand()->delete($table, $condition, $params)->execute();
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }
}
