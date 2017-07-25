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

    public static function getPayment($type){
        switch ($type) {
            case Wxpay::NAME:
                return Yii::$app->wxpay;
            case AliPay::NAME:
                break;
            default:
                throw new InvalidArgumentException(Yii::t('app', "{$type}不支持的支付类型"));
                break;
        }
    }
}
