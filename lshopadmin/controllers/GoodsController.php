<?php
namespace lshopadmin\controllers;

use Yii;
use common\controllers\AdminController as Controller;
use common\models\goods\GoodsModel;
use common\models\goods\ar\Goods;
use common\models\goods\ar\GoodsDetail;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use common\models\goods\query\GoodsQuery;


/**
 *
 */
class GoodsController extends Controller{
	public function actionUpdate($g_id){
		$goods = GoodsQuery::find()->andWhere(['=', 'g_id', $g_id])->one();
        if(!$goods){
			$this->setNotFoundWarning();
            return $this->redirect(['goods/list']);
        }
		// $form = new DynamicModel([
		// 	'g_id',
		// 	'g_code',
		// 	'g_primary_name',
		// 	'g_secondary_name',
		// 	'g_cls_id',
		// 	'g_status',
		// 	'g_create_uid',
		// 	'g_update_uid',
		// 	'g_created_at',
		// 	'g_updated_at',
		// 	'g_start_at',
		// 	'g_end_at',
		// 	'g_sku_attrs',
		// 	'g_metas',
		// 	'g_option_attrs',
		// 	'g_vaild_sku_ids',
		// 	'g_skus',
		// 	'g_source',
		// 	'gd_id',
		// 	'g_intro_text',
		// ]);
		// $form->load($goods->toArray(), '');
		return $this->render('update', [
			'goods' => $goods,
			'goodsDetail' => $goods->g_detail
		]);
	}
	public function actionCreate(){
		$form = new DynamicModel([
			'g_primary_name',
			'g_code',
			'g_intro_text',
			'g_cls_id',
			'g_metas',
			'g_sku_attrs'
		]);
		$gModel = new GoodsModel();
		$postData = Yii::$app->request->post("DynamicModel");
		if($postData){
			$postData['g_create_uid'] = $this->adminSession->u_id;
			$goods = $gModel->createGoods($postData);
			if($goods){
				$this->setCreateSuccess();
				return $this->refresh();
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
		$getData = Yii::$app->request->get();
        $query = GoodsQuery::find();
        $defaultOrder = [
			'g_created_at' => SORT_DESC,
			'g_updated_at' => SORT_DESC
		];
        $dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => $defaultOrder,
				'attributes' => [
					'g_created_at',
					'g_updated_at'
				]
			]
		]);
		return $this->render('list', [
			'dataProvider' => $dataProvider
		]);
	}
}
