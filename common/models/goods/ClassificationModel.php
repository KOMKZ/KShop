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
    public $maxLevel = 3;
    public function createGoodsClassification($data){
        $goodsCls = new GoodsClassification();
        if(!$goodsCls->load($data, '') || !$goodsCls->validate()){
            $this->addError('', $this->getOneErrMsg($goodsCls));
            return false;
        }
        if(!empty($data['g_cls_pid'])){
            $parents = GoodsClassificationQuery::findParentsById($data['g_cls_pid']);
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
