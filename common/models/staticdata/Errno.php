<?php
namespace common\models\staticdata;

use yii\base\Object;
/**
 *
 */
class Errno extends Object
{
    CONST EXCEPTION = 'EXCEPTION';
    CONST DB_FAIL_INSERT = 'DB_FAIL_INSERT';
    CONST DB_FAIL_UPDATE = 'DB_FAIL_UPDATE';
    CONST DB_FAIL_MDELETE = 'DB_FAIL_MDELETE';
}
