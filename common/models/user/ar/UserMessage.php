<?php
namespace common\models\user\ar;

use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
use common\models\message\MsgMode;
/**
 *
 */
class UserMessage extends ActiveRecord
{
    const STATUS_UNREAD = 'unread';
    const STATUS_HAD_READ = 'had_read';

    public static function tableName(){
        return "{{%user_message}}";
    }

    
    public function rules(){
        return [
            ['u_id', 'required']
            // todo checkexists

            // todo check exists
            ,['um_msg_id', 'default', 'value' => 0]

            ,['um_status', 'in', 'range' => ConstMap::getConst('um_status', true)]
            ,['um_status', 'default', 'value' => self::STATUS_UNREAD]

            // todo check exists
            ,['um_from_uid', 'default', 'value' => 0]

            ,['um_type', 'required']
            ,['um_type', 'in', 'range' => ConstMap::getConst('message_type', true)]

            ,['um_content_type', 'required']
            ,['um_content_type', 'in', 'range' => ConstMap::getConst('message_content_type', true)]

            ,['um_content', 'required']
            ,['um_content', 'string']

        ];
    }
}
