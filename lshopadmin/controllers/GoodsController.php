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
use yii\helpers\ArrayHelper;
use common\models\goods\ar\GoodsMeta;


/**
 *
 */
class GoodsController extends Controller{
	public function getUrls(){
		return [
			'goods-update' => ['goods/update'],
			'g-meta-delete' => ['goods/ajax-delete-g-meta']
		];
	}

	public function actionAjaxDeleteGMeta($gm_id, $g_id){
		$goods = GoodsQuery::find()->andWhere(['=', 'g_id', $g_id])->one();
		if(!$goods){
			$this->setNotFoundWarning();
			return $this->error(1, "数据不存在");
		}
		$updateData['g_del_meta_ids'][] = $gm_id;
		$gModel = new GoodsModel();
		$result = $gModel->updateGoods($updateData, $goods);
		if(!$result){
			$this->setError(implode(',', $gModel->getErrors()));
			return $this->error(1, implode(',', $gModel->getErrors()));
		}else{
			$this->setDeleteSuccess();
		}
		return $this->succ();

	}
	public function actionPjaxListGMetas($g_id){
		$goods = GoodsQuery::find()->andWhere(['=', 'g_id', $g_id])->one();
		if(!$goods){
			$this->setNotFoundWarning();
		}
		return $this->renderPartial('pjax-list-g-metas', [
			'goods' => $goods,
			'urls' => $this->getUrls()
		]);
	}
	public function actionPjaxSaveGMetas($gm_id, $g_id){
		$meta = GoodsQuery::findMetas()->andWhere(['=', 'gm_id', $gm_id])->one();
		if(!$meta){
			$meta = new GoodsMeta();
		}
		$updateData = [];
		$gModel = new GoodsModel();
		$goods = GoodsQuery::find()->andWhere(['=', 'g_id', $g_id])->one();
		$postData = Yii::$app->request->post("DynamicModel");
		if(!$goods){
			$this->setNotFoundWarning("商品数据不存在");
			// return $this->refresh();
		}elseif(!$meta->isNewRecord && $postData){
			// update
			$updateData['g_metas'][] = $postData;
		}elseif($meta->isNewRecord && $postData){
			$updateData['g_metas'][] = $postData;
		}
		$form = new DynamicModel([
			'gm_id',
			'g_atr_id',
			'g_atr_name',
			'gm_value',
		]);
		$form->addRule([
			'gm_id',
			'g_atr_id',
			'g_atr_name',
			'gm_value',
		], 'safe');
		if($updateData){
			$result = $gModel->updateGoods($updateData, $goods);
			if(!$result){
				$this->setErrorFromErrors($gModel->getErrors());
				$form->addErrors($gModel->getErrors());
			}else{
				$this->setSaveSuccess();
				$meta->refresh();
			}
		}
		$form->setAttributes($meta->toArray(), false);
		return $this->renderPartial("pjax-save-g-metas", [
			'model' => $form,
			'urls' => $this->getUrls()
		]);
	}
	public function actionUpdate($g_id){
		$goods = GoodsQuery::find()->andWhere(['=', 'g_id', $g_id])->one();
        if(!$goods){
			$this->setNotFoundWarning();
            return $this->redirect(['goods/list']);
        }
		$gModel = new GoodsModel();
		$postData = Yii::$app->request->post();
		if($postData){
			$postData = array_merge(
				ArrayHelper::getValue($postData, 'Goods', []),
				ArrayHelper::getValue($postData, 'GoodsDetail', [])
			);
			$result = $gModel->updateGoods($postData, $goods);
			if($result){
				$this->setUpdateSuccess();
				return $this->refresh();
			}

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
			'goodsDetail' => $goods->g_detail,
			'urls' => $this->getUrls()
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
