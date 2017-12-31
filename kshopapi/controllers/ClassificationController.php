<?php
namespace kshopapi\controllers;

use Yii;
use kshopapi\controllers\ApiController;
use common\models\goods\query\GoodsClassificationQuery;
use common\models\goods\ClassificationModel;
use common\models\goods\ar\GoodsClassification;
/**
 *
 */
class ClassificationController extends ApiController
{
    public function actionIndex(){
        $result = GoodsClassificationQuery::findClsAsTree();
        return $this->succ($result);
    }
    public function actionUpdate(){
        $postData = Yii::$app->request->getBodyParams();
		if(empty($postData['g_cls_id'])){
			return $this->error(400, Yii::t('app',"参数不完整，没有指定分类id"));
		}
        $cls = GoodsClassificationQuery::find()->andWhere(['=', 'g_cls_id', $postData['g_cls_id']])->one();
        if(!$cls){
			return $this->error(404, Yii::t('app', "指定的用户不存在"));
        }
        $clsModel = new ClassificationModel();
        $result = $clsModel->updateGoodsClassification($cls, $postData);
		if(!$result){
			return $this->error(null, $uModel->getErrors());
		}
		return $this->succ($result->toArray());
    }
    public function actionCreate(){
        $postData = Yii::$app->request->getBodyParams();
        $cls = new GoodsClassification();
        $clsModel = new ClassificationModel();
        $result = $clsModel->createGoodsClassification($cls, $postData);
        if(!$result){
            return $this->error(null, $clsModel->getErrors());
        }
        return $this->succ($result);
        
    }
}
