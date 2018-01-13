<?php
namespace common\models\goods;

use Yii;
use common\models\Model;
use common\models\goods\GoodsAttrModel;
use common\models\goods\ar\Goods;
use common\models\goods\ar\GoodsAttr;
use common\models\goods\ar\GoodsRealOption;
use common\models\goods\ar\GoodsDetail;
use common\models\goods\ar\GoodsSource;
use common\models\goods\query\GoodsAttrQuery;
use common\models\goods\query\GoodsSkuQuery;
use common\helpers\ArrayHelper;
use common\models\goods\ar\GoodsSku;
use common\models\staticdata\Errno;

/**
 *
 */
class GoodsModel extends Model
{
	/**
	 * 创建基础商品对象数据
	 * 请不要直接使用这个方法,使用以下方法代替
	 * \common\models\goods\GoodsModel::createGoods
	 * @param  array $data 基础商品数据
	 * - g_cls_id: integer,required 商品分类id
	 * - g_primary_name: string, required, 商品第一名称
	 * - g_secondary_name: string, 商品第二名称
	 * - g_status: string, 商品状态
	 * - g_create_uid: 商品创建用户id
	 * - g_start_at: 商品上架有效时间
	 * - g_end_at: 商品下架的时间
	 * @see \common\models\goods\ar\Goods::rules 了解详细的验证规则
	 * @return \common\models\goods\ar\Goods
	 */
	protected function createGoodsBase($data){
		// 首先创建基础属性
		if(!$goods = $this->validateGoodsBaseData($data)){
			return false;
		}
		if(!$goods->insert(false)){
			$this->addError(Errno::DB_FAIL_INSERT, Yii::t('app', '创建商品基础信息失败'));
			return false;
		}
		return $goods;
	}

	/**
	 * 创建商品信息说明数据
	 * @param  array $data  商品详细信息说明数据
	 * - g_intro_text: 商品介绍本文
	 * @param  Goods  $goods 商品基础数据
	 * 该对象必须在数据库存在
	 * @return \common\models\goods\ar\goodsDetail;
	 */
	protected function createGoodsDetail($data, Goods $goods){
		// 创建详细说明
		$data['g_id'] = $goods->g_id;
		if(!$goodsDetail = $this->validateGoodsDetailData($data)){
			return false;
		}
		if(!$goodsDetail->insert(false)){
			$this->addError(Errno::DB_FAIL_INSERT, Yii::t('app', '创建商品详细信息失败'));
			return false;
		}
		$goods->g_detail = $goodsDetail;
		return $goodsDetail;
	}

	/**
	 * 创建多个商品sku实例
	 * 该方法是 \common\models\goods\GoodsModel::createGoodsSku 的多个创建的封装
	 * 该方法将验证sku的逻辑值是否有效
	 * @see \common\models\goods\ar\Goods::g_valid_sku_ids;
	 * @param  array  $skuData sku数据
	 * 单个数据sku数据如下
	 * @see \common\models\goods\GoodsModel::createGoodsSku
	 * @param  Goods   $goods   商品基础对象
	 * @param  boolean $asArray 返回多个数据对象是否为数组
	 * @return array           返回多个sku数据数组
	 * 单个元素的数据结构:
	 * @see \common\models\goods\GoodsModel::createGoodsSku
	 */
	public function createMultiGoodsSku($skuData, Goods $goods, $asArray = false){
		$t = Yii::$app->db->beginTransaction();
		try {
			$skuData = ArrayHelper::index($skuData, 'g_sku_value');
			$skuIds = array_keys($skuData);
			$validSkuIds = array_keys(ArrayHelper::index($goods->g_vaild_sku_ids, 'value'));
			$notExistIds = array_diff($skuIds, $validSkuIds);
			if(!empty($notExistIds)){
				$this->addError('', Yii::t('app', "sku值不存在:" . implode(',', $notExistIds)));
				return false;
			}
			$skus = [];
			foreach($skuData as $skuItem){
				$sku = $this->createGoodsSku($skuItem, $goods);
				if(!$sku){
					return false;
				}
				$skus[] = $asArray ? $sku->toArray() : $sku;
			}
			$t->commit();
			return $skus;
		} catch (\Exception $e) {
			Yii::error($e);
			$this->addError(Errno::EXCEPTION, Yii::t('app', '创建商品sku异常'));
			return false;
		}
	}

	/**
	 * 更新商品的sku实例
	 * 该方法将分析出提交的数据是否存在新的sku和旧的sku，然后进行创建和返回,如果元素的g_sku_value不在当前的sku_values数组中，则说明该sku数据时新的sku数据
	 * 任何一个元素的创建/修改失败都导致这个流程的失败
	 * @see \common\models\goods\GoodsModel::createGoodsSku;
	 * @see \common\models\goods\GoodsModel::updateGoodsSku
	 * @param  array $skusData  sku数据对象
	 * 元素对象如下：
	 * @see \common\models\goods\GoodsModel::createGoodsSku
	 * @param  Goods  $goods    商品基础对象
	 * @return boolean          是否修改成功
	 */
	public function updateMultiGoodsSku($skusData, Goods $goods){
		$t = Yii::$app->db->beginTransaction();
		try {
			// 分析出新的还有旧的
			$oldSkus = $newSkus = [];
			$validSkus = $goods->g_vaild_sku_ids;
			$currentSkus = ArrayHelper::index($goods->g_skus, 'g_sku_value');
			foreach($skusData as $skuData){
				if(empty($skuData['g_sku_value']) || empty($validSkus[$skuData['g_sku_value']]))continue;
				if(array_key_exists($skuData['g_sku_value'], $currentSkus)){
					$oldSkus[] = $skuData;
				}else{
					$newSkus[] = $skuData;
				}
			}
			if($newSkus && !$this->createMultiGoodsSku($newSkus, $goods)){
				return false;
			}
			if(!empty($oldSkus)){
				foreach($oldSkus as $skuData){
					if(false === $this->updateGoodsSku($currentSkus[$skuData['g_sku_value']], $skuData, $goods)){
						return false;
					}
				}
			}
			$goods->refresh();
			$t->commit();
			//maybe change
			return true;
		} catch (\Exception $e) {
			Yii::error($e);
			$this->addError(Errno::EXCEPTION, Yii::t("app", "更新产品sku异常"));
			return false;
		}
	}

	/**
	 * 更新单个sku实例
	 * @param  GoodsSku $sku     商品sku实例对象
	 * @param  array   $skuData 需要修改的sku实例数据
	 * 数据结构查看:
	 * @see \common\models\goods\GoodsModel::createGoodsSku;
	 * @param  Goods    $goods   商品接触对象
	 * @return \common\models\goods\ar\GoodsSku;            返回修改后的sku实例对象
	 */
	public function updateGoodsSku(GoodsSku $sku, $skuData, Goods $goods){
		if(empty($sku->g_sku_id)){
			return false;
		}
		if(!$sku->load($skuData, '') || !$sku->validate()){
			return false;
		}
		if(false === $sku->update(false)){
			$this->addError(Errno::DB_FAIL_UPDATE, Yii::t('app', "更新产品sku失败"));
			return false;
		}
		return $sku;
	}

	/**
	 * 创建单个sku实例
	 * @param  array $skuData sku数据
	 * - g_sku_value: string,required 商品sku值
	 *   如：4:1;5:1, 注意商品sku值计算时通过sku属性和及选项值根据排列如何计算得到，该值必须在计算范围之内，
	 * - g_sku_stock_num: integer,required sku商品的库存值，default:0
	 * - g_sku_price: integer,required sku商品实际购买价格
	 * - g_sku_sale_price: integet,required, sku销售价格，展示用，空的时候则跟g_sku_price一致
	 * - g_sku_status: string, required ， sku的状态
	 * - g_sku_create_uid: integer, required, 创建用户的id，
	 * - g_id: integer, required 商品id
	 * @param  Goods    $goods   商品接触对象
	 * @return \common\models\goods\ar\GoodsSku;            返回修改后的sku实例对象
	 */
	public function createGoodsSku($skuData, Goods $goods){
		$sku = new GoodsSku();
		if(!$sku->load($skuData, '') || !$sku->validate()){
			$this->addError('', $this->getOneErrMsg($sku));
			return false;
		}
		$skuItems = ArrayHelper::index($goods->g_vaild_sku_ids, 'value');
		if(!array_key_exists($skuData['g_sku_value'], $skuItems)){
			$this->addError('', Yii::t('app', '无效的g_sku_value值:' . $skuData['g_sku_value']));
			return false;
		}
		// $sku->g_sku_id = static::buildGSkuId($goods->g_id, $sku->g_sku_value);
		$sku->g_sku_value_name = $skuItems[$sku->g_sku_value]['name'];
		$sku->g_sku_name = $sku->g_sku_name ? $sku->g_sku_name : $goods->g_primary_name;
		$sku->g_sku_created_at = time();
		if(!$sku->insert(false)){
			$this->addError(Errno::DB_FAIL_INSERT, Yii::t('app', '创建商品sku失败'));
			return false;
		}
		return $sku;
	}

	/**
	 * 构建商品sku存储值，本方法已经废弃
	 * @param  [type] $gid      [description]
	 * @param  [type] $skuValue [description]
	 * @return [type]           [description]
	 */
	public static function buildGSkuId($gid, $skuValue){
		return $gid . preg_replace('/[;:]/', '', $skuValue);
	}




	/**
	 * 创建一个商品
	 * 创建一个商品的流程如下：
	 * 1. 创建商品基础数据
	 * 2. 创建商品相信数据
	 * 3. 创建商品属性数据
	 * 4. 创建商品元数据
	 * @param  array $data 商品数据
	 * @see \common\models\goods\GoodsModel::createGoodsBase 了解如下数据结构
	 * - g_cls_id:
	 * - g_status:
	 * - g_primary_name:
	 * - g_secondary_name:
	 * - g_start_at:
	 * - g_end_at:
	 * - g_metas: array, required 商品元数据数组信息
	 *     @see \common\models\goods\GoodsAttrModel::createGoodsMetas 了解以下数据结构
	 * - g_attrs: array, required 商品属性数据数组信息
	 *     @see \common\models\goods\GoodsAttrModel::createGoodsAttrs 了解以下数据结构
	 * @return \common\models\goods\ar\Goods       返回商品基础数据对象
	 */
	public function createGoods($data){
		$t = Yii::$app->db->beginTransaction();
		try {
			// create goods base info
			if(!$goods = $this->createGoodsBase($data)){
				return false;
			}
			// create goods detail info
			if(!$goodsDetail = $this->createGoodsDetail($data, $goods)){
				return false;
			}
			$gAttrModel = new GoodsAttrModel();
			// create goods meta info
			$gMetas = $gAttrModel->createGoodsMetas([
				'metas' => ArrayHelper::getValue($data, 'g_metas', [])
			], $goods);
			if(!$gMetas){
				list($code, $error) = $gAttrModel->getOneError();
				$this->addError($code, "创建商品元属性失败:" . $error);
				return false;
			}
			// create goods attrs info,including option info
			$gAttrs = $gAttrModel->createGoodsAttrs([
				'attrs' => ArrayHelper::getValue($data, 'g_sku_attrs', [])
			], $goods);
			if(!$gAttrs){
				list($code, $error) = $gAttrModel->getOneError();
				$this->addError($code, "创建商品属性失败:" . $error);
				return false;
			}
			$t->commit();
			return $goods;
		} catch (\Exception $e) {
			Yii::error($e);
			$t->rollback();
			$this->addError(Errno::EXCEPTION, Yii::t('app', "创建商品发生异常"));
			return false;
		}
	}

	/**
	 * 删除一个商品
	 * 注意这个方法时伪删除，主要是改变商品的状态时删除状态
	 * @param  Goods  $goods 统一文件对象
	 * @return integer      影响的行数
	 */
	public function deleteGoods(Goods $goods){
		$goods->g_status = Goods::STATUS_DELETE;
		return $goods->update(false);
	}

	/**
	 * 通过商品sku属性构造出有效的sku值
	 * @param  Goods  $goods    商品sku基本对象
	 * @param  array $skuAttrs 商品sku属性
	 * 其中每个元素如下：
	 * - g_atr_id: integer, required sku属性的id
	 * - g_atr_show_name: string, required sku属性展示名称
	 * - g_atr_opts: array, required, sku属性选项值
	 *   其中每个元素如下：
	 *   - g_opt_value: integer,required sku属性选项值
	 *   - g_opt_name: string, required sku属性选项名称
	 * @return [array]           有效商品sku值
	 * 其中每个元素如下：
	 * - value: 商品sku展示值
	 * - name: 商品sku展示名称
	 */
	public static function buildValidSkuIds(Goods $goods, $skuAttrs){
		$skuValues = [];
		foreach($skuAttrs as $attr){
			$skuValues[$attr['g_atr_id']] = [];
			foreach($attr['g_atr_opts'] as $opt){
				$skuValues[$attr['g_atr_id']][] = [
					'value' => sprintf("%s:%s", $attr['g_atr_id'], $opt['g_opt_value']),
					'name'  => sprintf("%s-%s", $attr['g_atr_show_name'], $opt['g_opt_name']),
					'g_id' => $goods->g_id
				];
			}
		}
		ksort($skuValues);
		$skuIds = static::buildSkuIds($skuValues);
		return $skuIds;
	}

	/**
	 * 对输入属性的选项值进行组合情况输出
	 * @see common\models\goods\GoodsModel::buildValidSkuIds
	 * @param  [type] $skuValues sku属性数组：
	 * 其中每个元素的定义如下，要球元素的索引时属性id：
	 * - value: string,required sku属性id及选项值，如4:1
	 * - name: string, required sku属性id及选项值展示名称
	 * @return array            返回sku属性值和展示名称
	 * @see \common\models\goods\GoodsModel::buildValidSkuIds
	 */
	protected static function buildSkuIds($skuValues){
		if(empty($skuValues)){
			return [];
		}
		$skuIds = [];
		$first = array_shift($skuValues);
		foreach($first as $item){
			foreach($skuValues as $others){
				foreach($others as $otherItem){
					$skuIds[] = [
						'value' => implode(';', [$item['value'], $otherItem['value']]),
						'name' => implode(';', [$item['name'], $otherItem['name']]),
						'g_id' => $item['g_id']
					];
				}
				break;
			}
		}
		array_shift($skuValues);
		$next = array_shift($skuValues);
		if(!empty($next)){
			return static::buildSkuIds(array_merge([$skuIds], [$next]));
		}
		return $skuIds;
	}

	/**
	 * 创建一个商品资源
	 * @param  array $data     商品资源数据
	 * - gs_type: string, required 资源对象的类型
	 * - gs_sid: string, required 资源对象的id或者内容
	 * - gs_name: string, 资源对象的名称
	 * @param  object $clsObject 商品资源对象数据
	 * 由于商品资源具有分类，可能属于商品本神，也可能数据商品属性值，或者是商品sku对象
	 * 传入具体的绑定对象实现该资源只属于该对象
	 * 可用对象：
	 * \common\models\goods\ar\Goods
	 * \common\models\goods\ar\GoodsSku
	 * \common\models\goods\ar\GoodsRealOption
	 * @return \common\models\goods\ar\GoodsSource      商品资源对象
	 */
	public function createSource($data, $clsObject){
		$clsType = null;
		if($clsObject instanceof Goods){
			$clsType = GoodsSource::CLS_TYPE_GOODS;
			$clsId = $clsObject->g_id;
		}elseif($clsObject instanceof GoodsSku){
			$clsType = GoodsSource::CLS_TYPE_SKU;
			$clsId = $clsObject->g_sku_id;
		}elseif($clsObject instanceof GoodsRealOption){
			$clsType = GoodsSource::CLS_TYPE_OPTION;
			$clsId = $clsObject->g_opt_id;
		}
		$goodsSource = new GoodsSource();
		if(!$goodsSource->load($data, '') || !$goodsSource->validate()){
			return false;
		}
		$goodsSource->gs_cls_type = $clsType;
		$goodsSource->gs_cls_id = $clsId;
		$goodsSource->gs_created_at = time();
		if(!$goodsSource->insert(false)){
			$this->addError(Errno::DB_FAIL_INSERT, Yii::t('app', '创建商品相关资源失败'));
			return false;
		}
		return $goodsSource;
	}
	/**
	 * 验证并返回一个商品详细数据对象
	 * @param  array $data 商品详细数据
	 * @see \common]\models\goods\GoodsModel::createGoodsDetail
	 * @return \common\models\goods\ar\GoodsDetail       商品详细数据
	 */
	public function validateGoodsDetailData($data){
		$goodsDetail = new GoodsDetail();
		if(!$goodsDetail->load($data, '') || !$goodsDetail->validate()){
			$this->addErrors($goodsDetail->getErrors());
			return false;
		}
		return $goodsDetail;
	}

	/**
	 * 验证并返回一个商品基础数据对象
	 * @param  array $data 商品基础数据对象
	 * @see \common\models\goods\GoodsModel::createGoodsBase
	 * @return \common\models\goods\ar\Goods
	 */
	public function validateGoodsBaseData($data){
		$goods = new Goods();
		if(!$goods->load($data, '') || !$goods->validate()){
			$this->addErrors($goods->getErrors());
			return false;
		}
		$goods->g_created_at = time();
		return $goods;
	}

	/**
	 * 更新一个商品
	 * 注意该方法会检查出新数据然后创建，旧数据则更新
	 * 整体流程如下：
	 * 更新商品基础信息
	 * 检测是否是新的商品详细信息，是则创建，不是则更新
	 * 检测是否时新的商品元信息，是则创建，不是则更新
	 * 检测是否是新的商品属性信息，是则创建，不是则更新
	 * @param  array $data  更新一个商品的数据
	 * - base array 商品基础数据
	 *   @see \common\models\goods\GoodsModel::updateGoodsBase
	 * - detail: array 商品详细信息数据
	 *   @see \common\models\goods\GoodsModel::updateGoodsDetail
	 * - meta: array 商品元信息数据
	 *   @see \common\models\goods\GoodsModel::updateGoodsMetas
	 * - attrs: array 商品属性信息数据
	 *   @see \common\models\goods\GoodsAttrModel::updateGoodsAttrs
	 * - g_del_atr_ids: array 需要删除的属性id
	 * - g_del_meta_ids: array 需啊删除的元属性id
	 * @param  Goods  $goods [description]
	 * @return [type]        [description]
	 */
	public function updateGoods($data, Goods $goods){
		$t = Yii::$app->db->beginTransaction();
		try {
			if(empty($goods->g_id)){
				$this->addError('', Yii::t('app', "商品g_id不存在"));
				return false;
			}
			// update base data of goods
			if(!$goods = $this->updateGoodsBase($data, $goods)){
				return false;
			}
			// update or create detail data of goods
			$detailObject = $goods->g_detail;
			if($detailObject && (!$goodsDetail = $this->updateGoodsDetail($data, $goods))){
				return false;
			}
			if(!$detailObject && (!$goodsDetail = $this->createGoodsDetail($data, $goods))){
				return false;
			}
			// update, create, delete meta of goods
			$delRows = GoodsAttrModel::deleteGoodsMetas(['in', 'gm_id', ArrayHelper::getValue($data, 'g_del_meta_ids', [])]);
			if(false === $delRows){
				$this->addError(Errno::DB_FAIL_MDELETE, Yii::t('app', '删除多条商品元属性出错'));
				return false;
			}
			$oldMetaData = $newMetaData = [];
			foreach($data['g_metas'] as $metaData){
				if(!array_key_exists('gm_id', $metaData)){
					$newMetaData[] = $metaData;
				}elseif(array_key_exists('gm_id', $metaData) && !in_array($metaData['gm_id'], ArrayHelper::getValue($data, 'g_del_meta_ids', []))){ //
					$oldMetaData[] = $metaData;
				}
			}

			$gAttrModel = new GoodsAttrModel();
			if($oldMetaData && !$gAttrModel->updateGoodsMetas($oldMetaData, $goods)){
				$this->addErrors($gAttrModel->getErrors());
				return false;
			}
			if($newMetaData && !$gAttrModel->createGoodsMetas(['metas' => $newMetaData], $goods)){
				list($code, $error) = $gAttrModel->getOneError();
				$this->addError($code, "创建商品元属性失败:" . $error);
				return false;
			}
			unset($newMetaData, $oldMetaData);


			// 更新商品sku属性和选项属性
			// 首先进行进行删除操作
			if(!empty(ArrayHelper::getValue($data, 'g_del_atr_ids', []))){
				$delRows = GoodsAttrModel::deleteGoodsAttrs(['in', 'gr_id', ArrayHelper::getValue($data, 'g_del_atr_ids', [])]);
				if(false === $delRows){
					$this->addError(Errno::DB_FAIL_MDELETE, Yii::t('app', '删除多条商品属性出错'));
					return false;
				}
			}
			// 分别新的属性和旧的属性设置
			$oldAttrData = $newAttrData = [];
			foreach($data['g_sku_attrs'] as $attrData){
				if(!array_key_exists('gr_id', $attrData)){
					$newAttrData[] = $attrData;
				}elseif(array_key_exists('gr_id', $attrData) && !in_array($attrData['gr_id'], ArrayHelper::getValue($data, 'g_del_atr_ids', []))){ //
					$oldAttrData[] = $attrData;
				}
			}
			if($newAttrData && !$gAttrModel->createGoodsAttrs(['attrs' => $newAttrData], $goods)){
				list($code, $error) = $gAttrModel->getOneError();
				$this->addError($code, "创建商品属性失败:" . $error);
				return false;
			}
			if($oldAttrData && !$gAttrModel->updateGoodsAttrs($oldAttrData, $goods)){
				list($code, $error) = $gAttrModel->getOneError();
				$this->addError($code, "创建商品属性失败:" . $error);
				return false;
			}

			// 确保sku实例此时是正确的
			self::ensureSkuValid($goods);
			// $t->commit();
			$goods->refresh();
			return $goods;
		} catch (\Exception $e) {
			Yii::error($e);
			$t->rollBack();
			return false;
		}

	}

	/**
	 * 更新操作之前确保旧的sku记录的状态时否正确
	 * 本方法将计算最新有效的sku值，并将旧的sku值设置为无效
	 * @param  [type] $goods [description]
	 * @return [type]        [description]
	 */
	public static function ensureSkuValid($goods){
		$validSkuMap = static::buildValidSkuIds($goods, ArrayHelper::toArray($goods->g_sku_attrs));
		return GoodsSku::updateAll(['g_sku_status' => GoodsSku::STATUS_INVALID], [
			'and',
			['=', 'g_id', $goods->g_id],
			['not in', 'g_sku_value', array_keys(ArrayHelper::index($validSkuMap, 'value'))]
		]);
	}

	/**
	 * 更新商品详细数据
	 * @param  array $detailData 商品详细修改数据
	 * - g_intro_text: string 商品介绍信息
	 * @param  Goods  $goods      [description]
	 * @return \common\models\goods\ar\GoodsDetail;
	 */
	protected function updateGoodsDetail($detailData, Goods $goods){
		if(!empty($detailData)){
			$detailObj = $goods->g_detail;
			if(!$detailObj->load($detailData, '') || !$detailObj->validate()){
				$this->addError('', $this->getOneErrMsg($detailObj));
				return false;
			}
			if(false === $detailObj->update(false)){
				$this->addError(Errno::DB_FAIL_UPDATE, Yii::t('app', "更新商品详细数据失败"));
				return false;
			}
		}
		$goods->g_detail = $detailObj;
		return $detailObj;
	}

	/**
	 * 更新商品基础数据
	 * @param  array $baseData 商品修改的基础数据
	 * @see common\models\goods\GoodsModel::createGoods 了解哪些数据可以修改
	 * @param  Goods  $goods    [description]
	 * @return \common\models\goods\ar\Goods
	 */
	protected function updateGoodsBase($baseData, Goods $goods){
		if(!empty($baseData)){
			$goods->scenario = 'update';
			if(!$goods->load($baseData, '') || !$goods->validate()){
				$this->addErrors($goods->getErrros());
				return false;
			}
			if(false === $goods->update(false)){
				$this->addError(Errno::DB_FAIL_UPDATE, Yii::t('app', "更新商品基础数据失败"));
				return false;
			}
		}
		return $goods;
	}





}
