<?php
namespace common\models\goods\ar;

use Yii;
use common\models\staticdata\ConstMap;
use common\models\goods\ar\GoodsClassification;
use common\models\goods\ar\GoodsRealAttr;
use common\models\goods\ar\GoodsAttr;
use common\models\goods\ar\GoodsDetail;
use common\models\goods\ar\GoodsSku;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\goods\GoodsModel;
use yii\helpers\ArrayHelper;
/**
 *
 */
class Goods extends ActiveRecord
{
	CONST STATUS_DRAFT = 'draft';
	CONST STATUS_ON_SALE = 'on_sale';
	CONST STATUS_ON_NOT_SALE = 'on_not_sale';
	CONST STATUS_FORBIDDEN = 'forbidden';
	CONST STATUS_DELETE = 'delete';


	private $_g_valid_sku_ids = [];

	public $g_cls_id_label;
	public $g_start_at_string;
	public $g_end_at_string;


	public static function tableName(){
		return "{{%goods}}";
	}

	public function toArray(array $fields = [], array $expand = [], $recursive = true)
	{
		$data = parent::toArray($fields, $expand, $recursive);
		if(!empty($data['g_detail'])){
			$data = ArrayHelper::merge($data, $data['g_detail']);
			unset($data['g_detail']);
		}
		return $data;
	}

	public function fields(){
		$fields = parent::fields();
		return array_merge($fields, [
			'g_sku_attrs',
			'g_metas',
			'g_option_attrs',
			'g_vaild_sku_ids',
			'g_skus',
			'g_detail'
		]);
	}

	/**
	 * 获取有效的商品sku
	 * @return [type] [description]
	 */
	public function getG_skus(){
		return $this->hasMany(GoodsSku::className(), [
			'g_id' => 'g_id'
		])
		->select([
			"g_sku_id",
			"g_sku_name",
			"g_sku_value_name",
			"g_sku_value",
			"g_sku_stock_num",
			"g_sku_price",
			"g_sku_sale_price",
			"g_sku_status"
		])
		->andWhere([
			'in', 'g_sku_status', [GoodsSku::STATUS_ON_SALE]
		]);
	}

	/**
	 * 获取商品详细数据
	 * @return [type] [description]
	 */
	public function getG_detail(){
		return $this->hasOne(GoodsDetail::className(), ['g_id' => 'g_id']);
	}

	public function getG_storage(){
		return 0;
	}

	public function setG_detail($value){
		$this->populateRelation('g_detail', $value);
	}

	/**
	 * 获取商品属性
	 * @return [type] [description]
	 */
	public function getG_attrs(){
		$gaTable = GoodsAttr::tableName();
		$grTable = GoodsRealAttr::tableName();
		return $this
			   ->hasMany(GoodsRealAttr::className(), ['g_id' => 'g_id'])
			   ->leftJoin("{$gaTable}", "{$gaTable}.g_atr_id = {$grTable}.g_atr_id")
			   ->andWhere(['=', "{$grTable}.gr_status", GoodsRealAttr::STATUS_VALID]);
	}

	/**
	 * 获取有效的商品sku值及显示名称
	 * @return [type] [description]
	 */
	public function getG_vaild_sku_ids(){
		if(!$this->_g_valid_sku_ids){
			return $this->_g_valid_sku_ids = GoodsModel::buildValidSkuIds($this, ArrayHelper::toArray($this->g_sku_attrs));
		}
		return $this->_g_valid_sku_ids;
	}


	/**
	 * 获取商品sku属性
	 * @return [type] [description]
	 */
	public function getG_sku_attrs(){
		$gaTable = GoodsAttr::tableName();
		$grTable = GoodsRealAttr::tableName();

		return $this
			   ->hasMany(GoodsRealAttr::className(), ['g_id' => 'g_id'])
			   ->leftJoin("{$gaTable}", "{$gaTable}.g_atr_id = {$grTable}.g_atr_id")
			   ->andWhere(['=', "{$gaTable}.g_atr_type", GoodsAttr::ATR_TYPE_SKU])
			   ->andWhere(['=', "{$grTable}.gr_status", GoodsRealAttr::STATUS_VALID]);
	}

	public function getG_metas(){
		$gaTable = GoodsAttr::tableName();
		$gmTable = GoodsMeta::tableName();
		return $this
			   ->hasMany(GoodsMeta::className(), ['g_id' => 'g_id'])
			   ->leftJoin("{$gaTable}", "{$gaTable}.g_atr_id = {$gmTable}.g_atr_id")
			   ->select([
				   "{$gmTable}.gm_value",
				   "{$gmTable}.g_atr_id",
				   "{$gaTable}.g_atr_name",
			   ])
			   ->andWhere(['=', "$gaTable.g_atr_type", GoodsAttr::ATR_TYPE_META])
			   ->andWhere(['=', "{$gmTable}.gm_status", GoodsMeta::STATUS_VALID])
			   ->asArray();
	}

	public function setG_metas($value){
		$this->populateRelation('g_metas', $value);
	}

	/**
	 * 获取选项值属性
	 * @return [type] [description]
	 */
	public function getG_option_attrs(){
		$gaTable = GoodsAttr::tableName();
		$grTable = GoodsRealAttr::tableName();
		return $this
			   ->hasMany(GoodsRealAttr::className(), ['g_id' => 'g_id'])
			   ->leftJoin("{$gaTable}", "{$gaTable}.g_atr_id = {$grTable}.g_atr_id")
			   ->andWhere(['=', "$gaTable.g_atr_type", GoodsAttr::ATR_TYPE_OPTION])
			   ->andWhere(['=', "{$grTable}.gr_status", GoodsRealAttr::STATUS_VALID]);
	}



	public function scenarios(){
		return [
			'default' => [
				'g_cls_id', 'g_code', 'g_status', 'g_primary_name', 'g_secondary_name', 'g_create_uid', 'g_updated_at', 'g_start_at', 'g_end_at',
			],
			'update' => [
				'g_status', 'g_code', 'g_primary_name', 'g_secondary_name', 'g_start_at', 'g_end_at'
			]
		];
	}



	public function rules(){
		return [
			['g_cls_id', 'required'],
			['g_cls_id', 'exist', 'targetAttribute' => 'g_cls_id', 'targetClass' => GoodsClassification::className()],

			['g_code', 'required'],
            ['g_code', 'string', 'max' => 10],

			['g_primary_name', 'string'],
			['g_primary_name', 'required'],

			['g_secondary_name', 'string'],
			['g_secondary_name', 'default', 'value' => ''],

			['g_status', 'in', 'range' => ConstMap::getConst('g_status', true)],
			['g_status', 'default', 'value' => static::STATUS_DRAFT],

			// todo exist check
			['g_create_uid', 'required'],

			['g_updated_at', 'default', 'value' => time()],

			['g_start_at', 'filter', 'filter' => function($value){

				return $value ? strtotime($value) : $value;
			}],
			['g_start_at', 'integer'],

			// todo g_start_at < g_end_at 或者一个时期
			['g_end_at', 'filter', 'filter' => function($value){
				return $value ? strtotime($value) : $value;
			}],
			['g_end_at', 'integer']

		];
	}




}
