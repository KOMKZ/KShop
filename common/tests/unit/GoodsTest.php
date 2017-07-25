<?php
namespace common\tests;
use Yii;
use common\models\goods\ClassificationModel;
use common\models\goods\GoodsAttrModel;
use common\models\goods\query\GoodsAttrQuery;


class ClassificationTest extends \Codeception\Test\Unit
{
    use \Codeception\Specify;

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

    public function testCreateGoods(){
        Yii::$app->db->beginTransaction();
        // 1 分类数据

    }

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

        // 测试属性插入
        $attrs = [
            ['g_atr_code' => 'press_publish', 'g_atr_name' => '出版社', 'g_atr_cls_id' => $goodsCls['g_cls_id']],
            ['g_atr_code' => 'call_number', 'g_atr_name' => '书号', 'g_atr_cls_id' => $goodsCls['g_cls_id']],
            ['g_atr_code' => 'publication_date', 'g_atr_name' => '出版日期', 'g_atr_cls_id' => $goodsCls['g_cls_id']],
            ['g_atr_code' => 'theme', 'g_atr_name' => '题材', 'g_atr_cls_id' => $goodsCls['g_cls_id']],
            ['g_atr_code' => 'pages', 'g_atr_name' => '页数', 'g_atr_cls_id' => $goodsCls['g_cls_id']]
        ];
        $gAtrModel = new GoodsAttrModel();
        $rowsNum = $gAtrModel->createAttrs($attrs);
        if(!$rowsNum){
            $this->debug($gAtrModel->getOneError());
        }
        $this->tester->assertEquals(count($attrs), $rowsNum);

        $attrs = [
            ['g_atr_code' => 'code01', 'g_atr_name' => '作者02', 'g_atr_cls_id' => $childGoodsCls['g_cls_id']],
            ['g_atr_code' => 'code02', 'g_atr_name' => '作者03', 'g_atr_cls_id' => $childGoodsCls['g_cls_id']],
        ];
        $gAtrModel = new GoodsAttrModel();
        $rowsNum = $gAtrModel->createAttrs($attrs);
        if(!$rowsNum){
            $this->debug($gAtrModel->getOneError());
        }
        $this->tester->assertEquals(count($attrs), $rowsNum);


        $attrs = [
            ['g_atr_code' => 'code03', 'g_atr_name' => '出版社02', 'g_atr_cls_id' => $subChildGoodsCls['g_cls_id']],
            ['g_atr_code' => 'code04', 'g_atr_name' => '出版社03', 'g_atr_cls_id' => $subChildGoodsCls['g_cls_id']],
        ];
        $gAtrModel = new GoodsAttrModel();
        $rowsNum = $gAtrModel->createAttrs($attrs);
        if(!$rowsNum){
            $this->debug($gAtrModel->getOneError());
        }
        $this->tester->assertEquals(count($attrs), $rowsNum);




    }
}
