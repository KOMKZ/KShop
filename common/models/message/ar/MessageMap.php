<?php
namespace common\models\message\ar;

use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
use common\models\message\Message;
use common\models\message\ar\MessageTpl;

/**
 *
 */
class MessageMap extends ActiveRecord
{
    public static function tableName(){
        return "{{%message_map}}";
    }
    public function rules(){
        return [
            ['mm_type', 'in', 'range' => ConstMap::getConst('message_type', true)]
            ,['mm_type', 'default', 'value' => Message::TYPE_BOARD]

            ,['mm_content_type', 'in', 'range' => ConstMap::getConst('message_content_type', true)]

            ,['mm_content', 'validateContent']
            ,['mm_content', 'string']

            ,['mm_tpl_code', 'exist', 'targetClass' => MessageTpl::className(), 'targetAttribute' => 'mtpl_code']

            ,['mm_create_uid', 'required']

            ,['mm_receipt_uid', 'default', 'value' => 0]

            ,['mm_vars', 'string']

        ];
    }

    public function validateContent($attr){
        if(empty($this->$attr) && empty($this->mm_tpl_code)){
            $this->addError($attr, Yii::t('app', "content和tpl_code不能同时为空"));
        }
    }
}
