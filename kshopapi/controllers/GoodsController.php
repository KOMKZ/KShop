<?php
namespace kshopapi\controllers;

use Yii;
use common\models\goods\GoodsModel;
use kshopapi\controllers\ApiController;
use common\models\goods\query\GoodsAttrQuery;
use common\models\goods\ar\GoodsAttr;
use common\models\goods\query\GoodsQuery;
use common\models\goods\query\GoodsSkuQuery;
use common\models\goods\GoodsAttrModel;
use yii\data\ActiveDataProvider;
/**
 *
 */
class GoodsController extends ApiController{
    public function actionList(){
        $getData = Yii::$app->request->get();
        $query = GoodsQuery::find();
        $defaultOrder = [
			'g_created_at' => SORT_DESC,
			'g_updated_at' => SORT_DESC
		];
        $provider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => $defaultOrder,
				'attributes' => [
					'g_created_at',
					'g_updated_at'
				]
			]
		]);
		return $this->succItems($provider->getModels(), $provider->totalCount);
    }
    public function actionViewSku($g_id, $g_sku_value){
        $getData = Yii::$app->request->get();
        $goodsSku = GoodsSkuQuery::find()
                                 ->andWhere(['=', 'g_id', $g_id])
                                 ->andWhere(['=', 'g_sku_value', $g_sku_value])
                                 ->one();
        if(!$goodsSku){
            return $this->error(404, Yii::t('app', '指定的数据不存在'));
        }
        return $this->succ($goodsSku->toArray());
    }

    public function actionCreateSku(){
        $postData = Yii::$app->request->getBodyParams();
        if(empty($postData['g_id'])){
            return $this->error(500, Yii::t('app', '参数不完整'));
        }
        $goods = GoodsQuery::find()->andWhere(['=', 'g_id', $postData['g_id']])->one();
        if(!$goods){
            return $this->error(404, Yii::t('app', '指定的商品不存在'));
        }
        $loginUser = Yii::$app->user->identity;
        $postData['g_sku_create_uid'] = $loginUser->u_id;
        $skuData = [$postData];
        $gModel = new GoodsModel();
		$skus = $gModel->createMultiGoodsSku($skuData, $goods);
		if(!$skus){
            return $this->error(1, $gModel->getErrors());
		}
        $sku = array_pop($skus);
        return $this->succ($sku->toArray());
    }

    public function actionUpdate(){
        $postData = Yii::$app->request->getBodyParams();
        if(empty($postData['g_id'])){
            return $this->error(500, Yii::t('app', '参数不完整'));
        }
        $goods = GoodsQuery::find()->andWhere(['=', 'g_id' , $postData['g_id']])->one();
        if(!$goods){
            return $this->error(404, Yii::t('app', '指定的商品不存在'));
        }
        $loginUser = Yii::$app->user->identity;
        $postData['g_update_uid'] = $loginUser->u_id;
        $goodsModel = new GoodsModel();
        $result = $goodsModel->updateGoods($postData, $goods);
        if(!$result){
            return $this->error(500, $goodsModel->getErrors());
        }
        return $this->succ($result->toArray());
    }
    public function actionCreate(){
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
    public function actionView($g_id){
        $goods = GoodsQuery::find()->andWhere(['=', 'g_id', $g_id])->one();
        if(!$goods){
            return $this->error(404, Yii::t('app', '指定的商品不存在'));
        }
        return $this->succ($goods->toArray());
    }
    public function actionCreateClsAttr(){
        $postData = Yii::$app->request->getBodyParams();
        $attrModel = new GoodsAttrModel();
        $goodsAttr = new GoodsAttr();
        $result = $attrModel->createAttr($postData, $goodsAttr);
        if(!$result){
            return $this->error(null, $attrModel->getErrors());
        }
        return $this->succ($goodsAttr->toArray());
    }
    public function actionDeleteClsAttr(){
        $postData = Yii::$app->request->getBodyParams();
        if(empty($postData['g_atr_id'])){
            return $this->succ(0);
        }
        $attr = GoodsAttrQuery::find()->andWhere(['=', 'g_atr_id', $postData['g_atr_id']])->one();
        if(!$attr){
            return $this->succ(0);
        }
        return $this->succ($attr->delete());
    }
    public function actionClsAttrs(){
        $getData = Yii::$app->request->get();
        if(!empty($getData['g_cls_id'])){
            $query = GoodsAttrQuery::findAttrsByClsid($getData['g_cls_id']);
        }else{
            $query = GoodsAttrQuery::find();
        }
        if(!empty($getData['g_cls_type'])){
            $query->andWhere(['=', 'g_atr_type', $getData['g_cls_type']]);
        }
        $query->andWhere(['=', 'g_atr_cls_type', GoodsAttr::ATR_CLS_TYPE_CLS]);
        return $this->succ($query->asArray()->all());
    }
}
