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
use yii\validators\FileValidator;
use yii\web\UploadedFile;
use common\models\file\FileModel;
use common\models\goods\query\GoodsOptionQuery;
use common\models\goods\ar\GoodsSource;

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

    public function actionCreateSource(){
        $postData = Yii::$app->request->getBodyParams();
        $file = UploadedFile::getInstanceByName('file');
        if($file){
            $fileValidator = new FileValidator([
                'extensions' => ['jpg', 'gif', 'png'],
                'maxSize' => 1 * 1024 * 1024, // 1m
            ]);
            $isValidFile = $fileValidator->validate($file, $error);
            if(!$isValidFile){
                return $this->error(500, $error);
            }
            // 上传到文件模块中
            $fileModel = new FileModel();
            $sourceData = [
                'file_source_path' => $file->tempName,
                'file_save_name' => $file->name,
                'file_is_tmp' => 0
            ];
            $file = $fileModel->createFileBySource($sourceData);
            if(!$file){
                return $this->error(1, $fileModel->getErrors());
            }
            $postData['gs_sid'] = $file['file_query_id'];
        }
        // 查找资源所属
        if(empty($postData['gs_cls_type'])){
            return $this->error(1, Yii::t('app', '缺失参数gs_cls_type'));
        }
        if(GoodsSource::CLS_TYPE_SKU == $postData['gs_cls_type']){
            // sku本身
            $clsObject = GoodsSkuQuery::find()->where(['g_sku_id' => $postData['gs_cls_id']])->one();
        }elseif(GoodsSource::CLS_TYPE_GOODS == $postData['gs_cls_type']){
            // 商品本身
            $clsObject = GoodsQuery::find()->where(['g_id' => $postData['gs_cls_id']])->one();
        }elseif($GoodsSource::CLS_TYPE_OPTION == $postData['gs_cls_type']){
            // 选项
            $clsObject = GoodsOptionQuery::find()->where(['g_opt_id' => $postData['gs_cls_id']])->one();
        }else{
            return $this->error(1, Yii::t('app', '无效参数值gs_cls_type'));
        }
        if(!$clsObject){
            return $this->error(1, Yii::t('app', '资源所属分类不存在'));
        }
        $gModel = new GoodsModel();
        $gSource = $gModel->createSource($postData, $clsObject);
        if(!$gSource){
            return $this->error(1, $gModel->getErrors());
        }
        return $this->succ($gSource->toArray());
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
