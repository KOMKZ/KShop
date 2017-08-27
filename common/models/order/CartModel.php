<?php
namespace common\models\order;

use common\models\Model;
use Yii;
use common\models\goods\ar\GoodsSku;
use common\models\user\ar\User;
use common\staticdata\Errno;
use common\models\order\ar\CartItem;
/**
 *
 */
class CartModel extends Model
{




    public function createCartItem($data){
        $cartItem = new CartItem();
        if(!$cartItem->load($data, '') || !$cartItem->validate()){
            $this->addError('', $this->getOneErrMsg($cartItem));
            return false;
        }
        if(!$cartItem->insert(false)){
            $this->addError(Errno::DB_INSERT_FAIL, Yii::t('app', "创建购物车条目失败"));
            return false;
        }
        return $cartItem;
    }


}
