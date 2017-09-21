<?php
namespace common\models\goods;

use Yii;
use common\models\Model;
use common\models\goods\ar\GoodsClassification;
use common\models\goods\query\GoodsClassificationQuery;

/**
 *
 */
class ClassificationModel extends Model
{
	public $maxLevel = 4;
	public function validateClsUpdate($data, GoodsClassification $goodsCls){
		$goodsCls->scenario = 'update';
		if(!$goodsCls->load($data) || !$goodsCls->validate()){
			return false;
		}
		return true;
	}

	public function updateGoodsClassification(GoodsClassification $goodsCls){
		if(false === $goodsCls->update(false)){
			$this->addError('', Yii::t('app', '更新失败'));
			return false;
		}
		return $goodsCls;
	}

	public function validateClsCreate($data, GoodsClassification $goodsCls){
		if(!$goodsCls->load($data) || !$goodsCls->validate()){
			return false;
		}
		return true;
	}

	public function removeClassification(GoodsClassification $goodsCls){
		$hasChild = GoodsClassificationQuery::find()
											->andWhere(['=', 'g_cls_pid', $goodsCls->g_cls_id])
											->asArray()
											->all();
		if($hasChild){
			$this->addError('', Yii::t('app', '指定的分类存在子分类，禁止删除'));
			return false;
		}
		// todo more check
		$goodsCls->delete();
		// todo more delete
		return true;
	}

	public function removeClsSafe($ids){
		// todo more delete
		return GoodsClassification::deleteAll(['g_cls_id' => $ids]);
	}

	public function createGoodsClassification(GoodsClassification $goodsCls){
		if(!empty($goodsCls->g_cls_pid)){
			$parents = GoodsClassificationQuery::findParentsById($goodsCls->g_cls_pid);
			if(count($parents) >= $this->maxLevel){
				$this->addError('', Yii::t('app', "分类层级不得超过{$this->maxLevel}级"));
				return false;
			}
		}
		$goodsCls->g_cls_created_at = time();
		if(!$goodsCls->insert(false)){
			$this->addError('', Yii::t('app', '插入失败'));
			return false;
		}
		return $goodsCls;
	}
}
