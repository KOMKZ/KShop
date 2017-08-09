<?php
namespace common\models\mail\ar;

use Yii;
use yii\db\ActiveRecord;
use common\models\staticdata\ConstMap;
use common\models\mail\MailModel;

/**
 *
 */
class Mail extends ActiveRecord
{
    const CONTENT_TYPE_HTML = 'text/html';
    const LIST_TYPE_INLINE = 'inline';

    public static function tableName(){
        return "{{%mail_record}}";
    }
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $data = parent::toArray($fields, $expand, $recursive);
        if(!empty($data['mail_meta_data'])){
            $data['mail_meta_data'] = json_decode($data['mail_meta_data'], true);
        }
        if(!empty($data['mail_cron_params'])){
            $data['mail_cron_params'] = json_decode($data['mail_cron_params'], true);
        }
        if(!empty($data['mail_attachments'])){
            $data['mail_attachments'] = json_decode($data['mail_attachments'], true);
        }
        return $data;
    }
    public function getMeta_data(){
        return json_decode($this->mail_meta_data, true);
    }
    public function getCron_params(){
        return json_decode($this->mail_cron_params, true);
    }
    public function getAttachments(){
        return json_decode($this->mail_attachments, true);
    }
    public function rules(){
        return [
            ['mail_title', 'required'],
            ['mail_title', 'string'],

            ['mail_content', 'required'],
            ['mail_content', 'string'],

            ['mail_content_type', 'required'],
            ['mail_content_type', 'in', 'range' => ConstMap::getConst('mail_content_type')],

            ['mail_is_cron', 'default', 'value' => 0],

            ['mail_is_cron', 'default', 'value' => ''],

            ['mail_meta_data', 'required'],
            ['mail_meta_data', 'validateMetaData'],
            ['mail_meta_data', 'default', 'value' => []],
            ['mail_meta_data', 'filter', 'filter' => [MailModel::className(), 'filterMetaData']],
            ['mail_meta_data', 'filter', 'filter' => 'json_encode'],

            ['mail_attachments', 'default', 'value' => []],
            ['mail_attachments', 'filter', 'filter' => [MailModel::className(), 'filterAttachments']],
            ['mail_attachments', 'validateAttachments'],
            ['mail_attachments', 'filter', 'filter' => 'json_encode'],

            ['mail_cron_params', 'validateCronParams'],
            ['mail_cron_params', 'default', 'value' => []],
            ['mail_cron_params', 'filter', 'filter' => [MailModel::className(), 'filterCronParams']],
            ['mail_cron_params', 'filter', 'filter' => 'json_encode']
        ];
    }

    public function validateMetaData($attr){
        $metaData = $this->$attr;
        if(empty($metaData['addresses']) || empty($metaData['addresses']['type']) || empty($metaData['addresses']['list'])){
            $this->addError($attr, Yii::t('app', "metadata中的addresses结构错误"));
        }
        if(!in_array($metaData['addresses']['type'], ConstMap::getConst('mail_list_type', true))){
            $this->addError($attr, Yii::t('app', "mail_list_type值{$metaData['addresses']['type']}不合法"));
        }

    }

    public function validateAttachments($attr){
        foreach($this->$attr as $item){
            if(!file_exists($item['file'])){
                $this->addError($attr, Yii::t('app', "{$item['file']}不存在"));
            }
        }
    }

    public function validateCronParams($attr){

    }
}
