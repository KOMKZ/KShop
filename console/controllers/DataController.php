<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use common\models\goods\ar\GoodsClassification;
use common\models\goods\ar\GoodsAttr;
use common\models\user\UserModel;
use common\models\user\ar\User;
use yii\di\ServiceLocator;


class DataController extends Controller{

	public function actionInstTestUser(){
		// 用户数据
		Yii::$app->db->createCommand()->truncateTable(User::tableName())->execute();
		$max = 3;
		$default = [
			'u_username' => 'kitralzhong%s',
			'password' => 'philips',
			'password_confirm' => 'philips',
			'u_email' => 'kitralzhong%s@qq.com',
			'u_auth_status' => 'had_auth',
			'u_status' => 'active',
		];
		$i = 0;
		$uModel = new UserModel();
		while($i <= $max){
			$defaultData = $default;
			$defaultData['u_username'] = sprintf($defaultData['u_username'], $i);
			$defaultData['u_email'] = sprintf($defaultData['u_email'], $i);
			$uModel->createUser($defaultData);
			$i++;
			echo $defaultData['u_username'] . "\n";
		}
	}

	public function actionInstTest(){


		$db = Yii::$app->db;
		$comm = $db->createCommand();

		try {
			$t = $db->beginTransaction();


			// 插入分类信息
			$db->createCommand()->truncateTable(GoodsClassification::tableName())->execute();
			$comm->batchInsert(GoodsClassification::tableName(), [
				"g_cls_id", "g_cls_name", "g_cls_show_name", "g_cls_pid", "g_cls_created_at"
			], [
				[1, "餐厨", "餐厨", 0, time()]
				,[2, "水具杯壶", "水具杯壶", 1, time()]
			]);
			$comm->execute();

			// 插入属性信息
			$db->createCommand()->truncateTable(GoodsAttr::tableName())->execute();
			$comm->batchInsert(GoodsAttr::tableName(), [
				"g_atr_id", "g_atr_code", "g_atr_type", "g_atr_opt_img",
				"g_atr_name", "g_atr_show_name", "g_atr_cls_id", "g_atr_cls_type", "g_atr_created_at"
			], [
				[1, "", "meta", 0, "材料", "材料", 1, "cls", time()]
				,[2, "", "meta", 0, "容量规格", "容量规格", 2, "cls", time()]
				,[3, "", "meta", 0, "质量执行标准", "质量执行标准", 1, "cls", time()]
				,[4, "", "meta", 0, "保温性能", "保温性能", 2, "cls", time()]
				,[5, "", "meta", 0, "特别说明", "特别说明", 1, "cls", time()]
				,[6, "", "sku", 0, "颜色", "颜色", 2, "cls", time()]
			]);
			$comm->execute();

			$t->commit();
		} catch (\Exception $e) {
			throw $e;
		}
	}
}
