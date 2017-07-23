<?php
namespace common\models\classification;

use Yii;
use common\models\Model;
use common\models\classification\ar\GoodsClassification;

/**
 *
 */
class ClassificationModel extends Model
{
    public function createGoodsClassification($data){
        
        $goodsCls = new GoodsClassification();
        if(!$goodsCls->load($data, '') || !$goodsCls->validate()){
            $this->addError('', $this->getOneErrMsg($goodsCls));
            return false;
        }
        $goodsCls->g_cls_created_at = time();
        if(!$goodsCls->insert(false)){
            $this->addError('', Yii::t('app', '插入失败'));
            return false;
        }
        return $goodsCls;
    }
}
