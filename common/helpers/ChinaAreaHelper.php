<?php
namespace common\helpers;

use Yii;
use yii\helpers\BaseFileHelper;

/**
 *
 */
class ChinaAreaHelper extends BaseFileHelper
{
    protected static $areas = [];
    public static function validateAreaId($id){
        $result = [];
        $areas = static::getAreas();
        @list($pid, $cid, $aid) = explode(':', $id);
        $pid = (int)$pid;
        $pMap = array_combine($areas['id0'], $areas['name0']);
        $pString = ArrayHelper::getValue($pMap, $pid, '');
        if(!$pString){
            return false;
        }
        if(null === $cid){
            return true;
        }
        $cid = (int)$cid;
        $cMap = array_combine($areas['id' . $pid], $areas['name' . $pid]);
        $cString = ArrayHelper::getValue($cMap, $cid, '');
        if(!$cString){
            return false;
        }
        if(null === $aid){
            return true;
        }
        $aid = (int)$aid;
        $aMap = array_combine($areas['id' . $cid], $areas['name' . $cid]);
        $aString = ArrayHelper::getValue($aMap, $aid, '');
        if(!$aString){
            return false;
        }
        return true;
    }
    public static function parseAreaIdAsString($id){
        $result = [];
        $areas = static::getAreas();
        @list($pid, $cid, $aid) = explode(':', $id);
        $pid = (int)$pid;
        $pMap = array_combine($areas['id0'], $areas['name0']);
        $pString = ArrayHelper::getValue($pMap, $pid, '');
        if(!$pString){
            return implode('', $result);
        }

        $result[] = $pString;
        $cid = (int)$cid;
        $cMap = array_combine($areas['id' . $pid], $areas['name' . $pid]);
        $cString = ArrayHelper::getValue($cMap, $cid, '');
        if(!$cString){
            return implode('', $result);
        }

        $result[] = $cString;
        $aid = (int)$aid;
        $aMap = array_combine($areas['id' . $cid], $areas['name' . $cid]);
        $aString = ArrayHelper::getValue($aMap, $aid, '');
        if(!$aString){
            return implode('', $result);
        }
        $result[] = $aString;
        return implode('', $result);
    }
    protected static function getAreas(){
        if(empty(static::$areas)){
            static::$areas  = require(Yii::getAlias("@common/helpers/dataarea.php"));
        }
        return static::$areas;
    }
}
