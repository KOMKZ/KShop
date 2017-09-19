<?php
namespace kshopadmin\controllers;

use Yii;
use common\controllers\AdminController;
use common\models\classification\ar\GoodsClassification;
use common\models\goods\ClassificationModel;
/**
 *
 */
class ClassificationController extends AdminController
{
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
