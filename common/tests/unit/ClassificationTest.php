<?php
namespace common\tests;
use Yii;
use common\models\goods\ClassificationModel;
use common\models\goods\GoodsClassificationQuery;



class ClassificationTest extends \Codeception\Test\Unit
{

    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function debug($data){
        console($data);
    }

    // tests
    public function testCreateGoodsClassification()
    {
        Yii::$app->db->beginTransaction();
        $clsModel = new ClassificationModel();
        $data = [
            'g_cls_name' => '图书',
        ];
        $goodsCls = $clsModel->createGoodsClassification($data);
        if(!$goodsCls){
            $this->debug($clsModel->getOneError());
        }
        $this->tester->assertNotEmpty($goodsCls);
        $this->tester->assertArrayHasKey('g_cls_id', $goodsCls);
        $this->tester->assertNotEmpty($goodsCls['g_cls_show_name']);
        $this->tester->assertEquals(0, $goodsCls['g_cls_pid']);

        $childData = [
            'g_cls_name' => '计算机图书',
            'g_cls_pid' => $goodsCls['g_cls_id']
        ];
        $childGoodsCls = $clsModel->createGoodsClassification($childData);
        if(!$childGoodsCls){
            $this->debug($clsModel->getOneError());
        }
        $this->tester->assertEquals($goodsCls['g_cls_id'], $childGoodsCls['g_cls_pid']);

        $childData = [
            'g_cls_name' => '编程图书',
            'g_cls_pid' => $childGoodsCls['g_cls_id']
        ];
        $subChildGoodsCls = $clsModel->createGoodsClassification($childData);
        if(!$childGoodsCls){
            $this->debug($clsModel->getOneError());
        }
        $this->tester->assertEquals($childGoodsCls['g_cls_id'], $subChildGoodsCls['g_cls_pid']);

        $childData = [
            'g_cls_name' => 'php图书',
            'g_cls_pid' => $subChildGoodsCls['g_cls_id']
        ];
        $result = $clsModel->createGoodsClassification($childData);

        $this->tester->assertFalse($result);
    }
}
