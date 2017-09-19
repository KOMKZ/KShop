<?php
namespace common\models\goods;

use Yii;
use common\models\Model;
use common\models\classification\ar\GoodsClassification;
use common\models\goods\query\GoodsClassificationQuery;

/**
 *
 */
class ClassificationModel extends Model
{
	public $maxLevel = 3;
	public function validateClsCreate($data, GoodsClassification $goodsCls){
		if(!$goodsCls->load($data) || !$goodsCls->validate()){
			return false;
		}
		return true;
	}

	public function createGoodsClassification(GoodsClassification $goodsCls){
		$this->addError('', 'adf');
		return false;
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
