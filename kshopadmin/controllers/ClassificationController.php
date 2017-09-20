<?php
namespace kshopadmin\controllers;

use Yii;
use common\controllers\AdminController;
use common\models\goods\ar\GoodsClassification;
use common\models\goods\query\GoodsClassificationQuery;
use common\models\goods\ClassificationModel;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
/**
 *
 */
class ClassificationController extends AdminController
{
	public function actionIndex(){
		$query = GoodsClassificationQuery::find()
										  ->andWhere(['=', 'g_cls_pid', 0]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		return $this->render('index', [
			'dataProvider' => $dataProvider
		]);
	}
	public function actionUpdate($id, $type = null){
		// Yii::$app->db->beginTransaction();
		$clsModel = new ClassificationModel();
		// 查找父级分类是否存在
		$parentCls = GoodsClassificationQuery::find()
									  ->andWhere(['=', 'g_cls_id', $id])
									  ->one();
		if(!$parentCls){
			throw new NotFoundHttpException(Yii::t('app', "指定的数据不存在"));
		}
		// 初始化子分类
		$newCls = new GoodsClassification();
		$newCls->g_cls_pid = $parentCls->g_cls_id;

		// 处理创建子分类请求
		if('create_sub_action' == $type &&
			Yii::$app->request->isPost &&
			$postData = Yii::$app->request->getBodyParams()
		){
			$createResult = [];
			if($clsModel->validateClsCreate($postData, $newCls)){
				$createResult = $clsModel->createGoodsClassification($newCls);
			}
			if($createResult){
				Yii::$app->session->setFlash('success', Yii::t('app', '创建成功'));
				return $this->refresh();
			}
			if(false === $createResult){
				list($code, $error) = $clsModel->getOneError();
				Yii::$app->session->setFlash('error', Yii::t('app', "{$code}:{$error}"));
			}
  		}

		// 更新父级分类信息
		if('update_parent_action' == $type &&
		 	Yii::$app->request->isPost &&
			$postData = Yii::$app->request->getBodyParams()
		){
			$updateResult = [];
			if($clsModel->validateClsUpdate($postData, $parentCls)){
				$updateResult = $clsModel->updateGoodsClassification($parentCls);
			}
			if($updateResult){
				Yii::$app->session->setFlash('success', Yii::t('app', '更新成功'));
				return $this->refresh();
			}
			if(false === $updateResult){
				list($code, $error) = $clsModel->getOneError();
				Yii::$app->session->setFlash('error', Yii::t('app', "{$code}:{$error}"));
			}
		}

		// 查找父级分类儿子
		$childClsQuery = GoodsClassificationQuery::findChildrenByCls($parentCls);
		$childClsProvider = new ActiveDataProvider([
			'query' => $childClsQuery
		]);

		return $this->render('update', [
			'model' => $parentCls,
			'newCls' => $newCls,
			'childClsProvider' => $childClsProvider,
			'routes' => [
				'create_sub_action' => Url::to([
					'classification/update',
					'type' => 'create_sub_action',
					'id' => $parentCls->g_cls_id,
				]),
				'update_parent_action' => Url::to([
					'classification/update',
					'type' => 'update_parent_action',
					'id' => $parentCls->g_cls_id
				])
			]
		]);
	}
	public function actionCreate(){
		$newCls = new GoodsClassification();
		$clsModel = new ClassificationModel();
		if(Yii::$app->request->isPost && $postData = Yii::$app->request->post()){
			$createResult = [];
			if($clsModel->validateClsCreate($postData, $newCls)){
				$createResult = $clsModel->createGoodsClassification($newCls);
			}
			if($createResult){
				Yii::$app->session->setFlash('success', Yii::t('app', '创建成功'));
				return $this->refresh();
			}
			if(false === $createResult){
				list($code, $error) = $clsModel->getOneError();
				Yii::$app->session->setFlash('error', Yii::t('app', "{$code}:{$error}"));
			}
		}
		return $this->render('create', [
			'model' => $newCls
		]);
	}
}
