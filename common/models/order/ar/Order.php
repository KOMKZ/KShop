<?php
namespace common\models\order\ar;

use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
use common\models\message\MsgMode;
/**
 *
 */
class Order extends ActiveRecord
{
    public static function tableName(){
        return "{{%order}}";
    }

    public function getOrigin_price(){
        
    }
}
