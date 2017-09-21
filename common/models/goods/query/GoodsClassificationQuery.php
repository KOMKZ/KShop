<?php
namespace common\models\goods\query;

use yii\base\Object;
use common\models\goods\ar\GoodsClassification;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
/**
 *
 */
class GoodsClassificationQuery extends Object{
	public static function find(){
		return GoodsClassification::find();
	}

	public static function findClsAsTree(){
		$allCls = [];
		foreach(self::find()->select(['g_cls_id', 'g_cls_name', 'g_cls_pid'])->asArray()->all() as $cls){
			$cls['nodes'] = [];
			$cls['text'] = $cls['g_cls_name'];
			$cls['href'] = Url::to(['classification/update', 'id' => $cls['g_cls_id']]);
			$allCls[$cls['g_cls_id']] = $cls;
		}
		foreach($allCls as $index => $cls){
			if(0 == $cls['g_cls_pid']){
				continue;
			}
			$fatherId = $cls['g_cls_pid'];
			$curId = $cls['g_cls_id'];
			$fatherNode = $allCls[$fatherId];
			if(is_string($fatherNode)){
				$allCls[$index] = static::setAsLeave($allCls, $fatherNode, $cls);
			}else{
				$allCls[$fatherId]['nodes'][$curId] = $cls;
				$allCls[$index] = $fatherId . ',' . $curId;
			}
		}
		foreach($allCls as $index => $item){
			if(is_string($item)){
				unset($allCls[$index]);
			}
		}
        $allCls = static::covertToArray($allCls);
        return $allCls;
	}

    protected static function covertToArray($allCls){
        $result = [];
        foreach($allCls as $index => $cls){
            if(!empty($cls['nodes'])){
                $cls['nodes'] = static::covertToArray($cls['nodes']);
            }
            $result[] = $cls;
        }
        return $result;
    }

	protected static function setAsLeave(&$allCls, $pidPath, $cls){
		$pidPathData = explode(',', $pidPath);
		$id = array_shift($pidPathData);
		$target = &$allCls[$id];
		while($id = array_shift($pidPathData)){
			$target = &$target['nodes'][$id];
		}
		$target['nodes'][$cls['g_cls_id']] = $cls;
		return $pidPath . ',' . $cls['g_cls_id'];
	}


	public static function findChildrenByCls($cls){
		$children = [];
		$query = self::find()
					 ->andWhere(['=', 'g_cls_pid', $cls->g_cls_id]);
		return $query;
	}


	/**
	 * 根据一个分类id获取该分类的所有父类
	 * @param  [type] $clsId [description]
	 * @return [type]        [description]
	 */
	public static function findParentsById($clsId){
		$parents = [];
		$one = GoodsClassification::find()
								  ->where([ 'g_cls_id' => $clsId])
								  ->one();
		if(!$one){
			return null;
		}elseif(0 == $one->g_cls_pid){
			return [$one];
		}else{
			$r = self::findParentsById($one->g_cls_pid);
			if(null !== $r){
				return array_merge($r, [$one]);
			}
		}
	}
}
