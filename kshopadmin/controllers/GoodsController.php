<?php
namespace kshopadmin\controllers;

use common\controllers\AdminController;

/**
 *
 */
class GoodsController extends AdminController
{
	public function actionCreate(){
		return $this->render('create');
	}
}
