<?php
namespace common\models\sms;

use common\models\Model;
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use yii\base\InvalidConfigException;
use common\models\sms\ar\Sms;
use common\models\set\SetModel;
use yii\helpers\ArrayHelper;
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

    /**
     * 创建短信，保存进数据库，推入推列，等待发送
     * @param  [type] $data [description]
     * - sms_provider: optional,default(Sms::PROVIDER_ALIDY)短信服务商,类型
     * - sms_type: required,短信的类型@see Sms::rules()
     * - sms_inner_code: required,短信的模板@see Sms::sms_inner_code
     * - sms_params_object: optional, 模板需要的变量
     * @return [type]       [description]
     */
    public function saveWaitSend($data){
        $sms = $this->createSms($data);
        if(!$sms){
            return false;
        }

    }

    public static function getInnerCodeMap($name = null){
        $innerCodes = ArrayHelper::index(SetModel::get('sms_inner_codes'), 'name');
        if(null === $name){
            return array_keys($innerCodes);
        }
        return $innerCodes[$name] ? $innerCodes[$name] : [];
    }

    public function createSms($data){
        $sms = new Sms();
        if(!$sms->load($data, '') || !$sms->validate()){
            $this->addErrors($sms->getErrors());
            return false;
        }
        // 检查参数是否匹配
        $paramsNum = preg_match_all('/\{([a-zA-Z0-9\-\_]+)\}/', $sms->sms_real_outer_code['message'], $requiredParams);
        if($paramsNum > 0  && count(array_keys($sms->sms_params_object)) != $paramsNum){
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
