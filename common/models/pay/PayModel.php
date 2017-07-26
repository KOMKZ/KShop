<?php
namespace common\models\pay;

use Yii;
use common\models\Model;
use common\models\pay\payment\Wxpay;
use common\models\pay\payment\AliPay;
use yii\base\InvalidArgumentException;
/**
 *
 */
class PayModel extends Model
{
    CONST NOTIFY_INVALID = 'notify_invalid';
    CONST NOTIFY_ORDER_INVALID = 'notify_order_invalid';
    public static function getPayment($type){
        switch ($type) {
            case Wxpay::NAME:
                return Yii::$app->wxpay;
            case AliPay::NAME:
                return Yii::$app->alipay;
            default:
                throw new InvalidArgumentException(Yii::t('app', "{$type}不支持的支付类型"));
                break;
        }
    }
}
