<?php
namespace common\models\sms;

use common\models\Model;
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use yii\base\InvalidConfigException;
use common\models\sms\ar\Sms;
/**
 *
 */
class SmsModel extends Model
{
    public $alidyDomain = 'dysmsapi.aliyuncs.com';
    public $alidyAccessKey = '';
    public $alidyAccessSecret = '';
    public $alidyRegion = 'cn-hangzhou';
    public $alidyEndPointName = 'cn-hangzhou';
    public $alidyProduct = 'Dysmsapi';
    public $alidySignName = '';


    private static $_clients = [];

    public static function getClient($type){
        if(!empty(self::$_clients[$type])){
            return self::$_clients[$type];
        }
        switch ($type) {
            case Sms::PROVIDER_ALIDY:
                return self::$_clients[$type] = $this->createAlidyClient();
                break;
            default:
                throw new InvalidConfigException("无效的配置{$type}");
                break;
        }
    }

    public function saveWaitSend($data){
        $sms = $this->createSms($data);
        if(!$sms){
            return false;
        }
        console($sms);
    }

    public function createSms($data){
        $sms = new Sms();
        if(!$sms->load($data, '') || !$sms->validate()){
            $this->addErrors($sms->getErrors());
            return false;
        }
        if(!$sms->insert(false)){
            $this->addError('', Yii::t('app', "数据库插入失败"));
            return false;
        }
        return $sms;
    }

    public function createAlidyClient(){
        Config::load();
        $profile = DefaultProfile::getProfile($this->alidyRegion, $this->alidyAccessKey, $this->alidyAccessSecret);
        DefaultProfile::addEndpoint($this->alidyEndPointNamendPointName, $this->alidyRegion, $this->alidyProduct, $this->alidyDomain);
        return new DefaultAcsClient($profile);
    }

}
