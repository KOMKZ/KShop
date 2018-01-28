<?php
namespace kshopapi\controllers;

use Yii;
use common\controllers\ApiController as Controller;
use yii\web\HttpException;
use yii\web\UserException;
use common\models\staticdata\ConstMap;
/**
 *
 */
class ApiController extends Controller
{
	public function behaviors(){
		$behaviors = parent::behaviors();
		$behaviors['bearerAuth']['optional'][] = 'site/index';
		$behaviors['bearerAuth']['optional'][] = 'site/error';
		$behaviors['bearerAuth']['optional'][] = 'site/enums-map';
		$behaviors['bearerAuth']['optional'][] = 'site/labels';
		$behaviors['bearerAuth']['optional'][] = 'file/output';
		$behaviors['bearerAuth']['optional'][] = 'user/view';
		$behaviors['bearerAuth']['optional'][] = 'classification/*';
		$behaviors['bearerAuth']['optional'][] = 'goods/*';

		return $behaviors;
	}




}
