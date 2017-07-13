<?php
namespace common\models\file\drivers;

use Yii;
use common\models\Model;
use yii\base\InvalidConfigException;
/**
 *
 */
class Disk extends Model
{
    CONST NAME = 'disk';
    protected $base = '';
    public function setBase($value){
        if(!is_dir($value)){
            throw new InvalidConfigException(Yii::t('app',"{$value} 路径不存在"));
        }
        $this->base = trim($value, '/');
    }
}
