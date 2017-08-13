<?php
namespace common\models\notify;

use Yii;
use common\models\Model;
use common\models\fortune\ar\Notify;

/**
 *
 */
class NotifyModel extends Model
{
    CONST OB_JSON = 1;

    CONST ROUTE_HTTP = 1;

    CONST S_NOT_SEND = 1;
    CONST S_SEND_COMPLETED = 2;
    CONST S_NO_FINISH = 4;
    CONST S_SEND_WITH_ERROR = 3;

    CONST STRING_SUCC = 'success';


    public static function notify($url, $data, $sign = false){
        throw new \Exception('http notify is not supported.');
    }

    public function executeNotify($data){
        $notify = $this->getOne($data);
        if(!$notify){
            $this->addError('', '指定的数据不存在');
            return false;
        }
        if(self::S_SEND_COMPLETED == $notify->nr_status){
            return $notify;
        }
        $trans = Yii::$app->db->beginTransaction();
        switch ($notify->nr_route_type) {
            case self::ROUTE_HTTP:
                $res = $this->sendByCurl($notify);
                break;
            default:
                throw new \Exception('不支持的通知路由类型');
                break;
        }
        if(0 != $res['code']){
            $status = self::S_SEND_WITH_ERROR;
        }else{
            $status = self::S_SEND_COMPLETED;
        }
        $notify->nr_updated_time = time();
        $notify->nr_lastnotify_time = time();
        $notify->nr_version = $notify->nr_version + 1;
        $notify->nr_status = $status;
        if(false === $notify->update(false)){
            Yii::error(['msg' => '通知执行失败', 'data' => $notify->toArray()]);
        }
        if(!$this->insertNewRes($notify->nr_id, json_encode($res))){
            Yii::error(['msg' => '返回数据记录失败', 'data' => $res]);
        }
        $trans->commit();
        return $notify;
    }

    protected function sendByCurl($notify){
        $res = ['data' => null, 'msg' => '', 'code' => 0];
        try {
            $timeout = !$notify->nr_timeout ? 20 : $notify->nr_timeout;
            $route = $notify->nr_route;
            $data = $notify->nr_ob_data;
            // todo以后支持更多的方式和参数，目前支持post, 还有json的content-type
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $route);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . mb_strlen($data, 'utf8'))
            );
            $result = curl_exec($ch);
            if(false == $result){
                $res['msg'] = curl_error($ch);
                $res['data'] = null;
                $res['code'] = (int)curl_errno($ch);
            }elseif(self::STRING_SUCC == $result){
                $res['data'] = $result;
            }
            return $res;
        } catch (\Exception $e) {
            Yii::error($e);
            $res['msg'] = '发生异常：' . $e->getMessage();
            $res['code'] = 1;
            return $res;
        }
    }

    protected function insertNewRes($nid, $res){
        return Yii::$app->db->createCommand()->insert('hh_notify_result', [
            'nrt_nid' => $nid,
            'nrt_result' => $res,
            'nrt_created_time' => time()
        ])->execute();
    }

    public function getOne($condition){
        if(is_object($condition)){
            return $condition;
        }
        if($condition){
            return Notify::find()->where($condition)->one();
        }else{
            return null;
        }
    }

    public function createNotify($data){
        // todo move rules to Notify ar
        if(!in_array($data['nr_ob_type'], self::getValidConsts('nr_ob_type', true))){
            $this->addError('', 'nr_ob_type is invalid.');
            return false;
        }
        if(!in_array($data['nr_route_type'], self::getValidConsts('nr_route_type', true))){
            $this->addError('', 'nr_route_type is invalid.');
            return false;
        }
        if(empty($data['nr_ob_data'])){
            $this->addError('', 'nr_ob_data cant not be empty.');
            return false;
        }
        if(empty($data['nr_route'])){
            $this->addError('', 'nr_route cant not be empty.');
            return false;
        }
        if(empty($data['nr_index'])){
            $data['nr_index'] = '';
        }else{
            $notify = $this->getOne(['nr_index' => $data['nr_index']]);
            if($notify){
                $this->addError('', "该通知的索引已经存在{$data['nr_index']}");
                return false;
            }
        }
        $isRepeat = (integer)$data['nr_is_repete'];
        $timeout = (integer)$data['nr_timeout'];

        $notify = new Notify();
        $notify->nr_index = $data['nr_index'];
        $notify->nr_ob_type = $data['nr_ob_type'];
        $notify->nr_ob_data = $data['nr_ob_data'];
        $notify->nr_created_time = time();
        $notify->nr_updated_time = time();
        $notify->nr_lastnotify_time = null;
        $notify->nr_version = 0;
        $notify->nr_route = $data['nr_route'];
        $notify->nr_route_type = $data['nr_route_type'];
        $notify->nr_is_repete = $isRepeat;
        $notify->nr_timeout = $timeout;
        $notify->nr_status = self::S_NOT_SEND;

        $affect = $notify->insert(false);
        if(!$affect){
            $this->addError('', '插入失败');
            return false;
        }
        return $notify;
    }

    static public $_constMap = [];
    public static function getValidConsts($type, $onlyValue = false){
        if(empty(self::$_constMap)){
            self::$_constMap = [
                'nr_ob_type' => [
                    self::OB_JSON => 'json'
                ],
                'nr_route_type' => [
                    self::ROUTE_HTTP => 'http'
                ]
            ];
        }
        if(array_key_exists($type, self::$_constMap) && !empty(self::$_constMap[$type])){
            return $onlyValue ? array_keys(self::$_constMap[$type]) : self::$_constMap[$type];
        }else{
            throw new \Exception("zh:不存在常量映射定义{$type}");
        }
    }


}
