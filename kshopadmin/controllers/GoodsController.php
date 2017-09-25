<?php
namespace kshopadmin\controllers;

use common\controllers\AdminController;
use common\models\goods\ar\Goods;
use common\models\goods\ar\GoodsAttr;
use common\models\goods\query\GoodsAttrQuery;
use common\models\goods\query\GoodsClassificationQuery;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/**
 *
 */
class GoodsController extends AdminController
{
	public function actionCreate(){
		$goods = new Goods();
		return $this->render('create', [
			'model' => $goods,
			'routes' => [
				'classification_result_action' => $this->getApiRoute(['classification/index']),
				'cls_meta_list_action' => Url::to(['goods/p-cls-attrs-list'])

			]
		]);
	}
	public function actionPClsAttrsList($id){
		try {
			$parentCls = GoodsClassificationQuery::find()
												->andWhere(['=', 'g_cls_id', $id])
												->one();
			if($parentCls){
				$clsMetaQuery = GoodsAttrQuery::findAttrsByClsid($parentCls->g_cls_id);
				$clsMetaQuery->andWhere(['=', 'g_atr_type' ,GoodsAttr::ATR_TYPE_META]);
				$clsMetasProvider = new ActiveDataProvider([
					'query' => $clsMetaQuery
				]);

				$clsAttrsQuery = GoodsAttrQuery::findAttrsByClsid($parentCls->g_cls_id);
				$clsAttrsQuery->andWhere(['!=', 'g_atr_type' ,GoodsAttr::ATR_TYPE_META]);
				$clsAttrsProvider = new ActiveDataProvider([
					'query' => $clsAttrsQuery
				]);

			}else{
				$clsMetasProvider = null;
				$clsAttrsProvider = null;
			}

			return $this->renderPartial('part-cls-attrs-list', [
				'clsMetasProvider' => $clsMetasProvider,
				'clsAttrsProvider' => $clsAttrsProvider,
				'model' => $parentCls,
				'error' => false
			]);
		} catch (\Exception $e) {
			return $this->renderPartial('part-cls-attrs-list', [
				'error' => $e->getMessage()
			]);
		}
	}




}
