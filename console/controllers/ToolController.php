<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use common\helpers\ChinaAreaHelper;
use PhpAmqpLib\Message\AMQPMessage;
use common\models\user\UserModel;

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;
use common\models\sms\ar\Sms;
use common\models\sms\SmsModel;
use common\base\Worker;
// 加载区域结点配置


class ToolController extends Controller{
	public $is_test = false;
	public $debug = false;


	public $_client = null;
	public $pidFile = '@app/runtime/logs/grab-pid.txt';
    public $logFile = '@app/runtime/logs/grab-log.txt';
	public $dataFile = '@app/runtime/logs/grab-data.txt';
	public function client(){
		if(!$this->_client){
			$this->_client = new \GuzzleHttp\Client();
		}
		return $this->_client;
	}
	public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['debug', 'is_test']
        );
    }
	public function debug($content){
		if($this->debug){
			echo $content . "\n";
		}
	}
	public function actionGrabPage($totalPage, $max){
		@file_put_contents(Yii::getAlias($this->dataFile));
		$worker = new Worker("tcp://127.0.0.1:2345");
        $worker->name = 'grab-worker';
        $worker::$logFile = Yii::getAlias($this->logFile);
        $worker::$pidFile = Yii::getAlias($this->pidFile);
        $worker->count = $max;
        $worker->onWorkerStart = function($worker) use($totalPage, $max)
        {
			$perTotal = ceil($totalPage/$max);
			$begin = $worker->id * $perTotal + 1;
			$end = ($worker->id + 1) * $perTotal;
			$end = $end > $totalPage ? $totalPage : $end;
			while($begin <= $end){
				$content = $this->grab($begin);
				$this->handle($content);
				$begin++;
			}
			echo "ok...\n";
        };
		Worker::$action = 'start';
		Worker::runAll();
	}
	public function handle($content){
		file_put_contents(Yii::getAlias($this->dataFile), $content . "\n", FILE_APPEND);
	}
	public function parseOne($one, $str){
		if(preg_match("/{$str}/", $one)){
			echo trim($one, "\n") . "\n";
		}
	}
	public function actionParse($str){
		$file = Yii::getAlias($this->dataFile);
		$handle = @fopen($file, "r");
		if ($handle) {
		    while (($buffer = fgets($handle, 4096)) !== false) {
		        $this->parseOne($buffer, $str);
		    }
		    if (!feof($handle)) {
		        echo "Error: unexpected fgets() fail\n";
		    }
		    fclose($handle);
		}
	}
	public function grab($page){
		$url = 'http://www.yiichina.com/topic?page=' . $page;
		$this->debug(sprintf("正在获取 %s", $url));
		$res = $this->client()->get('http://www.yiichina.com/topic?page=' . $page);
		$content = $res->getBody();
		$code = $res->getStatusCode();
		$result = preg_match_all(
			"/<h2 class=\"media\-heading\">.*?<a href=\"(.*?)\">(.*?)<\/a>.*?<\/h2>/",
			$content,
			$matches
		);
		if(!$result)return '';
		$list = [];
		foreach ($matches[1] as $index => $id) {
			$list[] = sprintf("%s %s", $id, $matches[2][$index]);
		}
		return implode("\n", $list);
	}

	public function actionTestGrab(){
		@file_put_contents(Yii::getAlias($this->dataFile), '');
		$url = "http://forum.laravelacademy.org/api/discussions?include=startUser%2ClastUser%2CstartPost%2Ctags&filter%5Bq%5D=%20tag%3Aquestion&";
		$ok = true;
		while($ok){
			$this->debug(sprintf("正在获取 %s", $url));
			$res = $this->client()->get($url);
			$content = $res->getBody();
			$code = $res->getStatusCode();
			$ok = $code == '200';
			if($ok){
				$data = json_decode($content, true);
				$list = [];
				foreach($data['data'] as $item){
					$list[] = sprintf("%s %s", $item['id'], $item['attributes']['title']);
				}
				$url = $data['links']['next'];
				$content = implode("\n", $list);
				file_put_contents(Yii::getAlias($this->dataFile), $content . "\n", FILE_APPEND);
			}else{
				echo "error\n";
			}

		}
	}

	public function actionSend(){
		Config::load();
		$product = "Dysmsapi";

        //产品域名,开发者无需替换
        $domain = "dysmsapi.aliyuncs.com";

        // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
        $accessKeyId = ""; // AccessKeyId

        $accessKeySecret = ""; // AccessKeySecret

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";

		$profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
		DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);
		$acsClient = new DefaultAcsClient($profile);

		// 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        // 必填，设置短信接收号码
        $request->setPhoneNumbers("13715194169");

        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName("安全家");

        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode("SMS_121225002");

        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        $request->setTemplateParam(json_encode(array(  // 短信模板中字段的值
            "obnumber"=>"123456789",
        ), JSON_UNESCAPED_UNICODE));

        // 可选，设置流水号
        // $request->setOutId("yourOutId");

        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
        // $request->setSmsUpExtendCode("1234567");

        // 发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
		console($acsResponse);
	}

	public function actionGeneLabels(){
		$sql = "show tables";
		$result = Yii::$app->db->createCommand($sql)->queryAll();
		$labels = [];
		foreach($result as $item){
			$schema = Yii::$app->db->getTableSchema(array_pop($item));
			foreach($schema->columns as $column){
				if($column->comment){
					$labels[$column->name] = $column->comment;
				}else{
					$labels[$column->name] = $column->name;
				}
			}
		}
		$file = Yii::getAlias('@common/models/staticdata/data/const_labels.php');
		$content = sprintf("<?php\nreturn %s;", VarDumper::export($labels));
		file_put_contents($file, $content);
	}
	public function actionBulk(){
		// Yii::$app->db->beginTransaction();
		$max = 100;
		$default = [
			'u_username' => 'kitralzhong%s',
			'password' => 'philips',
			'password_confirm' => 'philips',
			'u_email' => 'kitralzhong%s@qq.com',
			'u_auth_status' => 'had_auth',
			'u_status' => 'active',

		];
		$i = 0;
		$uModel = new UserModel();
		while($i <= $max){
			$defaultData = $default;
			$defaultData['u_username'] = sprintf($defaultData['u_username'], $i);
			$defaultData['u_email'] = sprintf($defaultData['u_email'], $i);
			$uModel->createUser($defaultData);
			$i++;
			echo $defaultData['u_username'] . "\n";
		}
	}

	public function actionDemo(){
		$in = "/home/master/tmp/hse/animation01.mp4";
		$out = "/home/master/tmp/hse/01.mp4";
		$waterMark = "/home/master/tmp/hse/hse.png";
		$ffmpeg = \FFMpeg\FFMpeg::create();
		$video = $ffmpeg->open($in);
		$format = new \FFMpeg\Format\Video\X264();
		$format->on('progress', function ($video, $format, $percentage) {
			echo "$percentage % transcoded\n";
		});
		$format->setAudioCodec("aac")
			->setKiloBitrate(2496);

		$video->filters()
			// ->resize(new \FFMpeg\Coordinate\Dimension(1024, 768))
			->watermark($waterMark, array(
				'position' => 'relative',
				'bottom' => 50,
				'left' => 50,
			));
		$video->save($format, $out);
	}


	public function actionOneConfig($app){
		$config = ArrayHelper::merge(
			require(Yii::getAlias('@common/config/merge_config.php')),
			require(Yii::getAlias("@{$app}/config/merge_config.php"))
		);
		ksort($config);
		file_put_contents(
			Yii::getAlias(sprintf("@{$app}/config/application%s.php", $this->is_test ? '-test' : '')),
			sprintf("<?php\nreturn %s;", VarDumper::export($config))
		);
	}
	public function actionDecode($string = '', $type = 'json'){
		echo json_decode('"' . $string . '"');
		echo "\n";
	}
	public function actionPublish(){
		$conn = Yii::$app->amqpConn;
		$channel = $conn->channel();
		$channel->queue_declare('email-job', false, true, false, false);
		$msg = new AMQPMessage(json_encode([
			'f_id' => 1
		]), ['delivery_mode' => 2]);
		$channel->basic_publish($msg, '', 'email-job');
	}

}
