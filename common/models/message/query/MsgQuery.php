<?php
namespace common\models\message\query;

use yii\base\Object;
use common\models\message\ar\MessageMap;
use common\models\user\ar\UserMessage;
use common\models\message\Message;

/**
 *
 */
class MsgQuery extends Object
{
    public static function findUnInsertBoardMsg($andWhere = []){
        // 联表查处用户信息表中um_msg_id最大的,然后主体大于这个um_msg_id的所有记录,该方法返回query
        $maxBoardId = UserMessage::find()
                                  ->andWhere($andWhere)
                                  ->max('um_msg_id');
        $maxBoardId = $maxBoardId ? $maxBoardId : 0;
        return MessageMap::find()
                          ->where([
                              'mm_receipt_uid' => 0,
                              'mm_type' => Message::TYPE_BOARD
                          ])
                          ->andWhere(['>', 'mm_id', $maxBoardId]);
    }

    public static function findUnReadUsrMsg(){
        return UserMessage::find()->where(['um_status' => UserMessage::STATUS_UNREAD]);
    }
    public static function findHadReadUsrMsg(){
        return UserMessage::find()->where(['um_status' => UserMessage::STATUS_HAD_READ]);
    }
}
