<?php
namespace common\models\message;

use Yii;
use common\models\Model;
use common\models\message\MsgModel;
use common\models\message\ar\MessageTpl;
use common\models\staticdata\ConstMap;
/**
 *
 */
class Message extends Model
{
    const CONTENT_TYPE_PLAIN = "plain";
    CONST CONTENT_TYPE_TEMPLATE = 'tpl';

    const TYPE_ONE = 'one';
    const TYPE_BOARD = 'board';


    public $content = '';

    public $content_type = self::CONTENT_TYPE_PLAIN;

    public $tpl_code = "";

    public $tpl_params = [];

    public $create_uid = null;

    public $receipt_uid = null;

    public $type = null;

    public function rules(){
        return [
            ['type', 'required'],
            ['type', 'in', 'range' => ConstMap::getConst('message_type', true)],

            ['content', 'validateContent', 'skipOnEmpty' => false],
            ['content', 'string'],

            ['content_type', 'required'],
            ['content_type', 'in', 'range' => ConstMap::getConst('message_content_type', true)],

            ['tpl_code', 'exist', 'targetClass' => MessageTpl::className(), 'targetAttribute' => 'mtpl_code'],

            ['create_uid', 'required'],
            //todo check exists

            ['receipt_uid', 'required']
            //todo check esists
        ];
    }


    public function validateContent($attr){
        if(empty($this->$attr) && empty($this->tpl_code)){
            $this->addError($attr, Yii::t('app', "content和tpl_code不能同时为空"));
        }
    }
    public function getFinalContent(){
        return MsgModel::buildFinalContent($this);
    }
}
