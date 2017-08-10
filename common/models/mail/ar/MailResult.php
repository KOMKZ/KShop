<?php
namespace common\models\mail\ar;

use yii\db\ActiveRecord;

/**
 *
 */
class MailResult extends ActiveRecord
{
    public static function tableName(){
        return "{{%mail_send_result}}";
    }

}
