<?php
namespace common\tests;
use Yii;
use common\models\goods\ClassificationModel;
use common\models\goods\GoodsAttrModel;
use common\models\goods\query\GoodsAttrQuery;
use common\models\goods\query\GoodsQuery;
use common\models\goods\ar\Goods;
use common\models\goods\GoodsModel;
use common\models\goods\ar\GoodsAttr;



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

    public function testUpdateGoods(){
        Yii::$app->db->beginTransaction();
        $draftGoods = GoodsQuery::find()
                                ->where(['g_status' => Goods::STATUS_DRAFT])
                                ->one();
        if(!$draftGoods){
            $this->debug("当前没有处于草稿的商品");
        }
        $data = [
            'g_id' => $draftGoods['g_id'],
        ];
        console($draftGoods->toArray());
    }


    public function testDeleteGoods(){
        return ;
        $gModel = new GoodsModel();
        $goods  = GoodsQuery::find()
                                ->where(['g_id' => 31])
                                ->one();
        $result = $gModel->deleteGoods($goods);
        if(!$result){
            console($gModel->getOneError());
        }
        console($result);
    }


    public function testCreateGoods(){
        return ;
        Yii::$app->db->beginTransaction();
        $data = [
            'g_cls_id' => 3,
            'g_status' => Goods::STATUS_DRAFT,
            'g_primary_name' => 'IPhone7',
            'g_secondary_name' => 'IPhone7',
            'g_start_at' => time(),
            'g_end_at' => time()+3600,
            'g_create_uid' => 1,
            'g_attrs' => [
                [
                    // 颜色
                    'g_atr_id' => 5,
                    'g_atr_opts' => [
                        [
                            'g_opt_name' => '金色',
                            'g_opt_img' => 'https://img11.360buyimg.com/n9/s40x40_jfs/t3148/124/1614329694/101185/b709b251/57d0c55cNa20597da.jpg'
                        ],
                        [
                            'g_opt_name' => '白色',
                            'g_opt_img' => 'https://img11.360buyimg.com/n9/s40x40_jfs/t3148/124/1614329694/101185/b709b251/57d0c55cNa20597da.jpg'
                        ],
                        [
                            'g_opt_name' => '亮黑色',
                            'g_opt_img' => 'https://img11.360buyimg.com/n9/s40x40_jfs/t3148/124/1614329694/101185/b709b251/57d0c55cNa20597da.jpg'
                        ],
                    ]
                ],
                [
                    // 型号
                    'g_atr_id' => 1,
                    'g_atr_opts' => "IPhone7"
                ],
                [
                    // 内存
                    'g_atr_id' => 4,
                    'g_atr_opts' => [
                        [
                            'g_opt_name' => '32G',
                        ],
                        [
                            'g_opt_name' => '128G',
                        ],
                        [
                            'g_opt_name' => '256G',
                        ],
                    ]
                ],
                [
                    // 是否带logo
                    'g_atr_name' => "logo",
                    'g_atr_opt_img' => 1,
                    'g_atr_code' => 'has_logo',
                    'g_atr_type' => 'has_logo',
                    'g_atr_type' => 'sku',
                    'g_atr_opts' => [
                        [
                            'g_opt_name' => '暗黑logo',
                            'g_opt_img' => 'https://img11.360buyimg.com/n9/s40x40_jfs/t3148/124/1614329694/101185/b709b251/57d0c55cNa20597da.jpg'
                        ],
                        [
                            'g_opt_name' => '强色logo',
                            'g_opt_img' => 'https://img11.360buyimg.com/n9/s40x40_jfs/t3148/124/1614329694/101185/b709b251/57d0c55cNa20597da.jpg'
                        ],
                    ]
                ]
            ],
            'g_intro_text' => "IPhone7很长的富文本介绍",
        ];
        $gModel = new GoodsModel();
        $goods = $gModel->createGoods($data);
        if(!$goods){
            console($gModel->getOneError());
        }
        $this->assertNotEmpty($goods);
    }

    public function testCreateGoodsClassification()
    {
        return ;
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
            ['g_atr_cls_type' => 'cls', 'g_atr_code' => 'press_publish', 'g_atr_name' => '出版社', 'g_atr_cls_id' => $goodsCls['g_cls_id']],
            ['g_atr_cls_type' => 'cls', 'g_atr_code' => 'call_number', 'g_atr_name' => '书号', 'g_atr_cls_id' => $goodsCls['g_cls_id']],
            ['g_atr_cls_type' => 'cls', 'g_atr_code' => 'publication_date', 'g_atr_name' => '出版日期', 'g_atr_cls_id' => $goodsCls['g_cls_id']],
            ['g_atr_cls_type' => 'cls', 'g_atr_code' => 'theme', 'g_atr_name' => '题材', 'g_atr_cls_id' => $goodsCls['g_cls_id']],
            ['g_atr_cls_type' => 'cls', 'g_atr_code' => 'pages', 'g_atr_name' => '页数', 'g_atr_cls_id' => $goodsCls['g_cls_id']]
        ];
        $gAtrModel = new GoodsAttrModel();
        $rowsNum = $gAtrModel->createAttrs($attrs);
        if(!$rowsNum){
            $this->debug($gAtrModel->getOneError());
        }
        $this->tester->assertEquals(count($attrs), $rowsNum);

        $attrs = [
            ['g_atr_cls_type' => 'cls', 'g_atr_code' => 'code01', 'g_atr_name' => '作者02', 'g_atr_cls_id' => $childGoodsCls['g_cls_id']],
            ['g_atr_cls_type' => 'cls', 'g_atr_code' => 'code02', 'g_atr_name' => '作者03', 'g_atr_cls_id' => $childGoodsCls['g_cls_id']],
        ];
        $gAtrModel = new GoodsAttrModel();
        $rowsNum = $gAtrModel->createAttrs($attrs);
        if(!$rowsNum){
            $this->debug($gAtrModel->getOneError());
        }
        $this->tester->assertEquals(count($attrs), $rowsNum);


        $attrs = [
            ['g_atr_cls_type' => 'cls', 'g_atr_code' => 'code03', 'g_atr_name' => '出版社02', 'g_atr_cls_id' => $subChildGoodsCls['g_cls_id']],
            ['g_atr_cls_type' => 'cls', 'g_atr_code' => 'code04', 'g_atr_name' => '出版社03', 'g_atr_cls_id' => $subChildGoodsCls['g_cls_id']],
        ];
        $gAtrModel = new GoodsAttrModel();
        $rowsNum = $gAtrModel->createAttrs($attrs);
        if(!$rowsNum){
            $this->debug($gAtrModel->getOneError());
        }
        $this->tester->assertEquals(count($attrs), $rowsNum);




    }
}
