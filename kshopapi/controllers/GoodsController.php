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
use yii\helpers\ArrayHelper;
use yii\validators\FileValidator;
use yii\web\UploadedFile;
use common\models\file\FileModel;
use common\models\goods\query\GoodsOptionQuery;
use common\models\goods\ar\GoodsSource;

class GoodsController extends ApiController{

    /**
     * @api get,/goods,Goods,获取商品列表
     * @return #global_res
     * - data object#goods_list_res,商品列表信息
     */
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


    /**
     * @api post,/source,Source,创建一个资源
     * - gs_cls_id required,integer,in_body,资源关联对象id
     * - gs_cls_type required,integer,in_body,资源关联对象的id类型
     * - gs_type required,string,in_body,资源的类型
     *
     * @return #global_res
     * - data object#source_item
     */
    public function actionCreateSource($index){
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
                'file_is_tmp' => 0,
                'file_category' => 'pub_img'
            ];
            $file = $fileModel->createFileBySource($sourceData);
            if(!$file){
                return $this->error(1, $fileModel->getErrors());
            }
            $postData['gs_sid'] = $file['file_query_id'];
            $postData['gs_name'] = $file['file_save_name'];
        }
        // 查找资源所属
        if(empty($postData['gs_cls_type'])){
            return $this->error(1, Yii::t('app', '缺失参数gs_cls_type'));
        }
        if(GoodsSource::CLS_TYPE_SKU == $postData['gs_cls_type']){
            // sku本身
            $clsObject = GoodsSkuQuery::find()->where(['g_sku_id' => $index])->one();
        }elseif(GoodsSource::CLS_TYPE_GOODS == $postData['gs_cls_type']){
            // 商品本身
            $clsObject = GoodsQuery::find()->where(['g_code' => $index])->one();
        }elseif($GoodsSource::CLS_TYPE_OPTION == $postData['gs_cls_type']){
            // 选项
            $clsObject = GoodsOptionQuery::find()->where(['g_opt_id' => $index])->one();
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

    /**
    * @api post,/goods/{g_code}/sku,Goods,创建商品sku记录
    * - g_code required,string,in_path,主商品id
    * - g_sku_value required,string,in_body,商品sku值
    * - g_sku_price required,integer,in_body,商品sku价格
    * - g_sku_stock_num required,integer,in_body,商品库存量
    *
    *
    * @return #global_res
    * - data object#goods_item,商品信息
     */
    public function actionCreateSku(){
        $postData = Yii::$app->request->getBodyParams();
        $queryData = Yii::$app->request->getQueryParams();
        $postData['g_code'] = ArrayHelper::getValue($queryData, 'index', null);
        if(empty($postData['g_code'])){
            return $this->error(500, Yii::t('app', '参数不完整'));
        }
        $goods = GoodsQuery::find()->andWhere(['=', 'g_code', $postData['g_code']])->one();
        if(!$goods){
            return $this->error(404, Yii::t('app', '指定的商品不存在'));
        }
        $loginUser = Yii::$app->user->identity;
        $postData['g_sku_create_uid'] = $loginUser->u_id;
        $postData['g_id'] = $goods['g_id'];
        $skuData = [$postData];
        $gModel = new GoodsModel();
        // 调用的是创建多条的接口
		$skus = $gModel->createMultiGoodsSku($skuData, $goods);
		if(!$skus){
            return $this->error(1, $gModel->getErrors());
		}
        $sku = array_pop($skus);
        return $this->succ($sku->toArray());
    }

    /**
     * @api put,/goods/{g_code},Goods,修改主商品
     * - g_code required,string,in_path,主商品编号
     * - g_primary_name optional,string,in_body,商品主名称
     * - g_secondary_name optional,string,in_body,商品第二名称
     *
     * @return #global_res
     * - data object#goods_item
     */
    public function actionUpdate($index){
        $postData = Yii::$app->request->getBodyParams();

        $goods = GoodsQuery::find()->andWhere(['=', 'g_code' , $index])->one();
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

    /**
     * @api post,/goods,Goods,创建商品主记录
     * - g_cls_id required,integer,in_body,商品分类id
     * - g_code required,string,in_body,商品编号
     * - g_primary_name required,string,in_body,商品第一名称
     * - g_intro_text required,string,in_body,商品简介
     * - g_metas required,array#g_meta_update_param,in_body,商品元信息设置列表
     * - g_sku_attrs required,array#g_attr_param,in_body,商品属性信息列表
     * - g_secondary_name optional,string,in_body,商品第二名称
     *
     * @return #global_res
     * - data object#goods_item,商品信息
     */
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

    /**
     * @api get,/goods/{g_code},Goods,获取主商品信息
     * - g_code required,string,in_path,商品编号
     *
     * @return #global_res
     * - data object#goods_item,主商品信息
     *
     */
    public function actionView($index){
        $goods = GoodsQuery::find()->andWhere(['=', 'g_code', $index])->one();
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

/**
 * @def #goods_list_res
 * - total_count integer,总数量
 * - items array#goods_item,商品列表信息
 *
 * @def #goods_item
 * - g_id integer,商品id
 * - g_code string,商品编号
 * - g_cls_id string,商品所属分类id
 * - g_status string,商品状态
 * - g_primary_name string,商品主要名称
 * - g_secondary_name string,商品第二名称
 * - g_sku_attrs array#g_sku_attr,商品sku属性列表
 * - g_metas array#g_meta,商片元属性列表
 * - g_option_attrs array#g_sku_attr,商品选项属性列表
 * - g_vaild_sku_ids array#valid_sku_id,商品有效sku id
 * - g_skus array#sku_item,商品sku
 * - g_intro_text string,商品介绍文本
 *
 * @def #sku_item
 * - sku_id integer,sku id值
 *
 * @def #valid_sku_id
 * - value string,sku值
 * - name string,sku值名称
 *
 * @def #g_sku_attr
 * - g_atr_id integer,属性id
 * - g_atr_opts array#g_atr_opt_item,选值值列表
 * - g_atr_name string,属性名称
 * - g_atr_show_name string,属性展示名称
 *
 * @def #g_atr_opt_item
 * - g_opt_value string,选项值
 * - g_opt_id integer,选项值id
 * - g_opt_img_url string,选项值关联图片url
 * - g_opt_img integer,选项值是否支持图片显示
 *
 * @def #g_meta
 * - g_atr_id integer,属性id
 * - gm_id integer,动态属性值id
 * - g_atr_name string,元属性名称
 * - gm_value string,元属性值
 *
 * @def #g_meta_param
 * - g_atr_id optional,integer,元属性id，指定这个属性说明使用原有属性id，没指定新属性则必须指定这个值
 * - g_atr_code optional,string,元属性编号，使用这个属性用于创建新的属性，属性的类型属于商品
 * - g_atr_name optional,string,元属性名称，使用这个属性用于创建新的属性，属性的类型属于商品
 * - gm_value required,string,元属性值
 *
 * @def #g_meta_update_param
 * - g_atr_id optional,integer,元属性id，指定这个属性说明使用原有属性id，没指定新属性则必须指定这个值
 * - g_atr_code optional,string,元属性编号，使用这个属性用于创建新的属性，属性的类型属于商品
 * - g_atr_name optional,string,元属性名称，使用这个属性用于创建新的属性，属性的类型属于商品
 * - gm_value required,string,元属性值
 * - gm_id optional,integer,动态元属性id，如果是更新旧的属性则必传
 *
 * @def #g_attr_param
 * - g_atr_id optional,string,属性id，指定这个属性说明使用原有属性id，没指定新属性则必须指定这个值
 * - g_atr_code optional,string,属性编号，使用这个属性用于创建新的属性，属性的类型属于商品
 * - g_atr_name optional,string,属性名称，使用这个属性用于创建新的属性，属性的类型属于商品
 * - g_atr_opts required,string,sku属性选项值，选项值使用逗号隔开
 *
 * @def #g_attr_update_param
 * - g_atr_id optional,string,属性id，指定这个属性说明使用原有属性id，没指定新属性则必须指定这个值
 * - g_atr_code optional,string,属性编号，使用这个属性用于创建新的属性，属性的类型属于商品
 * - g_atr_name optional,string,属性名称，使用这个属性用于创建新的属性，属性的类型属于商品
 * - g_atr_opts required,string,sku属性选项值，选项值使用逗号隔开
 * - gr_id optional,integer,动态属性id，如果是更新旧的属性则必须指定
 *
 * @df #source_item
 * - gs_sid string,文件id
 * - gs_content string,文件内容，对于图片来说是url
 * - gs_type string,资源用分类
 * - gs_cls_id integer,资源所属对象id
 * - gs_cls_type string,资源所属对象类型
 *
 */
