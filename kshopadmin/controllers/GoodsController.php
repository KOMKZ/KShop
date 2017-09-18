<?php
namespace kshopadmin\controllers;

use common\controllers\AdminController;
use common\models\goods\ar\Goods;

/**
 *
 */
class GoodsController extends AdminController
{
	public function actionCreate(){
		$goods = new Goods();
		return $this->render('create', [
			'model' => $goods
		]);
	}
}
