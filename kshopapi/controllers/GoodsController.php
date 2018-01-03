<?php
namespace kshopapi\controllers;

use Yii;
use common\models\goods\GoodsModel;
use kshopapi\controllers\ApiController;
/**
 *
 */
class GoodsController extends ApiController{
    public function actionCreate(){
        Yii::$app->db->beginTransaction();
        $postData = Yii::$app->request->getBodyParams();
        $loginUser = Yii::$app->user->identity;
        $postData['g_create_uid'] = $loginUser->u_id;
		$gModel = new GoodsModel();
		$goods = $gModel->createGoods($postData);
        if(!$goods){
            return $this->error(null, $gModel->getErrors());
        }
        return $this->succ($goods->toArray());
    }
}