<?php
namespace common\models\user\query;

use yii\base\Object;

/**
 *
 */
class UserMsgQuery extends Object
{
    public static function findUnInsertBoardMsg(){
        // 联表查处用户信息表中um_msg_id最大的,然后主体大于这个um_msg_id的所有记录,该方法返回query
    }
    
    public static function findUnReadUsrMsg(){

    }

    public static function findHadReadUsrMsg(){

    }
}
