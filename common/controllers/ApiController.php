<?php
namespace common\controllers;

use Yii;
use yii\web\Controller;
use common\filters\auth\HttpBearerAuth;
use yii\filters\RateLimiter;
/**
 *
 */
class ApiController extends Controller
{
	public $enableCsrfValidation = false;
	private function getRes(){
		return [
			'code' => null,
			'data' => null,
			'message' => null
		];
	}
	public function behaviors()
	{
		return [
			'corsFilter' => [
				'class' => \yii\filters\Cors::className(),
				'cors' => [
					'Origin' => ['*'],
					'Access-Control-Request-Method' => ['GET', 'POST'],
					'Access-Control-Request-Headers' => ['*'],
					'Access-Control-Allow-Credentials' => true,
					'Access-Control-Max-Age' => 86400,
					'Access-Control-Expose-Headers' => [],
				],
			],
			// 'rateLimiter' => [
			// 	'class' => RateLimiter::className(),
			// ],
			'bearerAuth' => [
				'class' => HttpBearerAuth::className(),
				'except' => ["user/list"],
				'optional' => ['auth/login']
			]
		];
	}

	public function notfound($error){
		return $this->error(404, $error ? $error : Yii::t('app', '数据不存在'));
	}
	public function succItems($items, $count = null){
		$res = $this->getRes();
		$res['data'] = [
			'items' => $items,
			'count' => null === $count ? count($items) : $count
		];
		$res['code'] = 0;
		$res['message'] = '';
		return $this->asJson($res);
	}
	public function succ($data = null){
		$res = $this->getRes();
		$res['data'] = $data;
		$res['code'] = 0;
		$res['message'] = '';
		return $this->asJson($res);
	}
	public function error($code, $message){
		$res = $this->getRes();
		$res['data'] = null;
		$res['code'] = empty($code) ? 1 : $code;
		$res['message'] = $message;
		return $this->asJson($res);
	}
}
