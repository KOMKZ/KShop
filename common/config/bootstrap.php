<?php
use yii\base\Event;
use yii\web\User;
use common\models\goods\ar\Goods;
use common\models\pay\ar\PayTrace;
use common\models\trans\TransModel;
use common\models\trans\ar\Transaction;
use common\models\user\UserModel;
use common\models\action\ActionModel;
use common\models\order\OrderModel;
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@kshopapi', dirname(dirname(__DIR__)) . '/kshopapi');
Yii::setAlias('@lshopadmin', dirname(dirname(__DIR__)) . '/lshopadmin');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@OSS', '@common/lib/alisdk/OSS');
Yii::setAlias('@Aliyun', '@common/lib/alisdk/dysms');

require(dirname(__DIR__) . '/lib/wxsdk/wxpay/lib/WxPay.Config.php');
require(dirname(__DIR__) . '/lib/wxsdk/wxpay/lib/WxPay.Exception.php');
require(dirname(__DIR__) . '/lib/wxsdk/wxpay/lib/WxPay.Data.php');
require(dirname(__DIR__) . '/lib/wxsdk/wxpay/lib/WxPay.Notify.php');
require(dirname(__DIR__) . '/lib/wxsdk/wxpay/lib/WxPay.Api.php');
require(dirname(__DIR__) . '/lib/alisdk/alipay/AopSdk.php');
require(dirname(__DIR__) . '/lib/Spyc.php');


Event::on(User::className(), User::EVENT_AFTER_LOGOUT, [UserModel::className(), "handleAfterLogout"]);
// 交易模块：绑定支付单支付成功处理事件
Event::on(PayTrace::className(), PayTrace::EVENT_AFTER_PAYED, [TransModel::className(), "handleReceivePayedEvent"]);
// 用户模块：绑定交易单支付成功处理事件
Event::on(Transaction::className(), Transaction::EVENT_AFTER_PAYED, [UserModel::className(), 'handleReceivePayedEvent']);
Event::on(Transaction::className(), Transaction::EVENT_AFTER_PAYED, [OrderModel::className(), 'handleReceivePayedEvent']);
Event::on(Goods::className(), Goods::EVENT_AFTER_UPDATE, [ActionModel::className(), "handle"]);

// fix bug https://github.com/auth0/auth0-PHP/issues/56
\Firebase\JWT\JWT::$leeway = 50;

\Yii::$container->set('yii\bootstrap\ActiveForm', [
    'fieldClass' => "common\widgets\ActiveField",
]);
