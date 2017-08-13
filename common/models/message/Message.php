<?php
namespace common\models\message;

use common\models\Model;

/**
 *
 */
class Message extends Model
{
    const CONTENT_TYPE_PLAIN = "plain";
    const TYPE_ONE = 'one';
    const TYPE_BOARD = 'board';

    public $content = '';

    public $content_type = self::CONTENT_TYPE_PLAIN;

    public $tpl_code = "";

    protected $tpl = null;

    public $tpl_params = [];

    public $create_uid = null;

    public $receipt_uid = null;

    public $type = null;

    public function rules(){
        return [
            ['type', 'required']
        ];
    }
}
