<?php
use yii\base\Event;
use yii\web\User;
use common\models\goods\ar\Goods;
use common\models\pay\ar\PayTrace;
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@kshopapi', dirname(dirname(__DIR__)) . '/kshopapi');
Yii::setAlias('@kshopadmin', dirname(dirname(__DIR__)) . '/kshopadmin');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@OSS', '@common/lib/alisdk/OSS');

require(dirname(__DIR__) . '/lib/wxsdk/wxpay/lib/WxPay.Config.php');
require(dirname(__DIR__) . '/lib/wxsdk/wxpay/lib/WxPay.Exception.php');
require(dirname(__DIR__) . '/lib/wxsdk/wxpay/lib/WxPay.Data.php');
require(dirname(__DIR__) . '/lib/wxsdk/wxpay/lib/WxPay.Notify.php');
require(dirname(__DIR__) . '/lib/wxsdk/wxpay/lib/WxPay.Api.php');
require(dirname(__DIR__) . '/lib/alisdk/alipay/AopSdk.php');

Event::on(User::className(), User::EVENT_AFTER_LOGOUT, ["\common\models\user\UserModel", "handleAfterLogout"]);
Event::on(PayTrace::className(), PayTrace::EVENT_AFTER_PAYED, ['\common\models\trans\TransModel', "handleReceivePayedEvent"]);
Event::on(Goods::className(), Goods::EVENT_AFTER_UPDATE, ["\common\models\action\ActionModel", "handle"]);
