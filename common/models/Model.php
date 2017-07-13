<?php
namespace common\models;

use yii\base\Model as BaseModel;

/**
 *
 */
class Model extends BaseModel
{
    public function getOneError(){
        $errors = $this->getFirstErrors();
        if(!empty($errors)){
            foreach($errors as $code => $msg){
                return [$code, $msg];
            }
        }else{
            return [null, null];
        }
    }
    public function getOneErrMsg($obj){
        return implode(',', $obj->getFirstErrors());
    }
}
