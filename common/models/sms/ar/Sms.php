<?php
namespace common\models\sms\ar;

use yii\db\ActiveRecord;

/**
 *
 */
class Sms extends ActiveRecord
{
    const PROVIDER_ALIDY = 'alidy';

    /**
     * 广播消息
     * @var string
     */
    const TYPE_BOARD = 'board';

    /**
     * 私信
     * @var string
     */
    const TYPE_PRIVATE = 'private';

    public static function tableName(){
        return "hh_sms";
    }
    public function rules(){
        return [
            ['sms_provider']
        ];
    }
}
