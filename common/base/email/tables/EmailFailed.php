<?php
namespace common\models\email\tables;

use yii\db\ActiveRecord;

/**
 *
 */
class EmailFailed extends ActiveRecord
{
    public static function tableName(){
        return "{{%email_failed}}";
    }
    public function attributeLabels(){
        return [
            'emf_id' => '失败id',
            'emf_data' => '邮件原始数据',
            'emf_code' => '邮件错误代码',
            'emf_message' => '邮件错误信息',
            'emf_created_at' => '错误时间'
        ];
    }
}
