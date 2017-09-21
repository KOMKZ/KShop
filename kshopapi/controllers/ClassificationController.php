<?php
namespace kshopapi\controllers;

use common\controllers\ApiController;
use common\models\goods\query\GoodsClassificationQuery;
/**
 *
 */
class ClassificationController extends ApiController
{
    public function actionIndex(){
        $result = GoodsClassificationQuery::findClsAsTree();
        return $this->succ($result);
    }
}
