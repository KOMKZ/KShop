<?php
namespace lshopadmin\controllers;

use Yii;
use common\controllers\AdminController as Controller;
use common\models\goods\GoodsModel;
use common\models\goods\ar\Goods;
use common\models\goods\ar\GoodsDetail;
use yii\base\DynamicModel;

/**
 *
 */
class GoodsController extends Controller{
	public function actionCreate(){
		Yii::$app->db->beginTransaction();
		$form = new DynamicModel([
			'g_primary_name',
			'g_code',
			'g_intro_text',
			'g_cls_id',
			'g_metas',
			'g_sku_attrs'
		]);
		$gModel = new GoodsModel();
		$form->g_metas = [
			['g_atr_id' => 1, "gm_value" => 'hello world']
		];
		$postData = Yii::$app->request->post("DynamicModel");
		if($postData){
			$postData['g_create_uid'] = $this->adminSession->u_id;
			$goods = $gModel->createGoods($postData);
			if($goods){
			}else{
				$this->setErrorFromErrors($gModel->getErrors());
				$form->addErrors($gModel->getErrors());
			}
		}
		return $this->render('create', [
			'model' => $form
		]);
	}

	public function actionList(){

	}
}
