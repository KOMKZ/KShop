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
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
/**
 *
 */
class ClassificationController extends AdminController
{
	public function actionDemo(){
		$result = GoodsClassificationQuery::findClsAsTree();
		console($result);
		return $this->render('demo');
	}
	public function actionBulk(){
		$postData = Yii::$app->request->getBodyParams();
		$type = ArrayHelper::getValue($postData, 'action', null);
		if(!$type){
			return $this->goBack();
		}
		if('bulk_delete_action' == $type){
			return $this->actionBulkDelete($postData);
		}
	}
	private function actionBulkDelete($data){
		$clsModel = new ClassificationModel();
		$deleteClsIds = ArrayHelper::getValue($data, 'selection', []);
		$report = [];
		$validIds = [];
		foreach($deleteClsIds as $id){
			$cls = GoodsClassificationQuery::find()->andWhere(['=', 'g_cls_id', $id])->one();
			if(!$cls){
				$report[] = sprintf("%s,%s", $id, Yii::t('app', '数据不存在'));
				continue;
			}
			if(!$clsModel->removeClassification($cls)){
				list($code, $error) = $clsModel->getOneError();
				$report[] = sprintf("%s, %s, %s", $cls->g_cls_id, $code, $error);
				continue;
			}
			$validIds[] = $id;
		}
		if($report){
			$this->setWarning(implode("<br/>", $report));
		}else{
			ClassificationModel::removeClsSafe($validIds);
			$this->setDeleteSuccess();
		}
		return $this->goBack();
	}
	public function actionIndex(){
		$query = GoodsClassificationQuery::find()
										  ->andWhere(['=', 'g_cls_pid', 0]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		$this->setReturnUrl();
		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'routes' => [
				'bulk_action' => Url::to(['classification/bulk']),
				'update_route' => ['classification/update'],
				'delete_route' => ['classification/delete']
			]
		]);
	}
	public function actionDelete($id){
		if(!Yii::$app->request->isPost){
			$this->setWarning("删除方法必须使用post请求");
			return $this->goBack();
		}
		$cls = GoodsClassificationQuery::find()->andWhere(['=', 'g_cls_id', $id])->one();
		if(!$cls){
			$this->setWarning("指定的删除不存在");
			return $this->goBack();
		}
		$clsModel = new ClassificationModel();
		if(!$clsModel->removeClassification($cls)){
			list($code, $error) = $clsModel->getOneError();
			$this->setError("{$code}:{$error}");
			return $this->goBack();
		}
		$this->setDeleteSuccess();
		return $this->goBack();
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
		// 查找该分类的所有父类
		if($parentCls->g_cls_id > 0){
			$parents = GoodsClassificationQuery::findParentsById($parentCls->g_cls_id);
		}else{
			$parents = [];
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
				$this->setInfo(Html::a(Yii::t('app','管理刚刚创建的分类'), Url::to(['classification/update', 'id' => $createResult->g_cls_id])), true);
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
		$this->setReturnUrl();
		return $this->render('update', [
			'model' => $parentCls,
			'newCls' => $newCls,
			'childClsProvider' => $childClsProvider,
			'parents' => $parents,
			'routes' => [
				'bulk_action' => Url::to(['classification/bulk']),
				'create_sub_action' => Url::to([
					'classification/update',
					'type' => 'create_sub_action',
					'id' => $parentCls->g_cls_id,
				]),
				'delete_parent_action' => Url::to([
					'classification/delete',
					'id' => $parentCls->g_cls_id
				]),
				'update_parent_action' => Url::to([
					'classification/update',
					'type' => 'update_parent_action',
					'id' => $parentCls->g_cls_id
				]),
				'update_route' => ['classification/update'],
				'delete_route' => ['classification/delete']
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
				$this->setInfo(Html::a(Yii::t('app','管理刚刚创建的分类'), Url::to(['classification/update', 'id' => $createResult->g_cls_id])), true);
				$this->setCreateSuccess();
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
