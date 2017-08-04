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
use common\models\goods\ar\GoodsSku;



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
        // return ;
        Yii::$app->db->beginTransaction();
        $goods = GoodsQuery::find()
                                ->where(['g_status' => Goods::STATUS_DRAFT])
                                ->one();
        if(!$goods){
            $this->debug("当前没有处于草稿的商品");
        }
        $data = [
            'base' => [
                'g_primary_name' => '苹果IPhone7',
                'g_secondary_name' => '苹果IPhone7',
                'g_cls_id' => 4,
                'g_status' => 'draft',
                'g_update_uid' => 2,
                'g_start_at' => time(),
                'g_end_at' => time() + 3600,
            ],
            'detail' => [
                'g_intro_text' => 'IPhone7非常长的富文本介绍',
            ],
            'meta' => [
                'g_metas' => [
                    [
                        // 型号
                        'gm_id' => 1,
                        'gm_value' => "苹果IPhone7"
                    ],
                    [
                        'g_atr_code' => 'resolution',
                        'g_atr_name' => '分辨率',
                        'gm_value' => '1334*750',
                    ],
                    [
                        'g_atr_code' => 'made_in',
                        'g_atr_name' => '商品产地',
                        'gm_value' => '中国大陆',
                    ],
                ],
            ],
            'g_del_meta_ids' => [1],
            'g_del_atr_ids' => [1],
            'attrs' => [
                'g_attrs' => [
                    [
                        // 颜色
                        'gr_id' => 2,
                        'g_atr_opts' => [
                            [
                                'g_opt_id' => 3,
                                'g_opt_name' => '玫瑰金',
                                'g_opt_img' => 'https://img11.360buyimg.com/n9/s40x40_jfs/t3148/124/1614329694/101185/b709b251/57d0c55cNa20597da.jpg'
                            ],
                        ]
                    ],
                    [
                        // 是否带logo
                        'g_atr_name' => '套餐',
                        'g_atr_opt_img' => 0,
                        'g_atr_code' => 'package',
                        'g_atr_type' => 'sku',
                        'g_atr_opts' => [
                            [
                                'g_opt_name' => '电信套餐',
                            ],
                            [
                                'g_opt_name' => '联通套餐',
                            ],
                        ]
                    ]
                ]
            ],
            'sku' => [
                [
                    'g_sku_value' => '4:1;5:1',
                    'g_sku_stock_num' => 100,
                    'g_sku_price' => '499899',
                    'g_sku_sale_price' => '529899',
                    'g_sku_status' => GoodsSku::STATUS_ON_NOT_SALE,
                    'g_sku_update_uid' => 1,
                    'g_id' => $goods->g_id,
                ],
                [
                    'g_sku_value' => '4:2;5:1',
                    'g_sku_stock_num' => 100,
                    'g_sku_price' => '579899',
                    'g_sku_sale_price' => '599899',
                    'g_sku_status' => GoodsSku::STATUS_ON_NOT_SALE,
                    'g_sku_update_uid' => 1,
                    'g_id' => $goods->g_id,
                ],
                [
                    'g_sku_value' => '4:3;5:3',
                    'g_sku_stock_num' => 100,
                    'g_sku_price' => '678799',
                    'g_sku_sale_price' => '698799',
                    'g_sku_status' => GoodsSku::STATUS_ON_NOT_SALE,
                    'g_sku_create_uid' => 2,
                    'g_id' => $goods->g_id,
                ],
            ]
        ];
        $gModel = new GoodsModel();
        $result = $gModel->updateGoods($data, $goods);
        if(!$result){
            $this->debug($gModel->getOneError());
        }
        console($goods->toArray());
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
            'g_metas' => [
                [
                    // 型号
                    'g_atr_id' => 1,
                    'gm_value' => "IPhone7"
                ],
            ],
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
                    'g_atr_type' => 'option',
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
        // console($goods->toArray());
        $this->assertNotEmpty($goods);
    }

    public function testCreateSku(){
        return ;
        Yii::$app->db->beginTransaction();
        $gModel = new GoodsModel();
        $goods  = GoodsQuery::find()
                                ->where(['g_id' => 1])
                                ->one();
        // 商品的sku信息
        $skuData = [
            [
                'g_sku_value' => '4:1;5:1',
                'g_sku_stock_num' => 100,
                'g_sku_price' => '499900',
                'g_sku_sale_price' => '529900',
                'g_sku_status' => GoodsSku::STATUS_ON_NOT_SALE,
                'g_sku_create_uid' => 1,
                'g_id' => $goods->g_id,
            ],
            [
                'g_sku_value' => '4:2;5:1',
                'g_sku_stock_num' => 100,
                'g_sku_price' => '579900',
                'g_sku_sale_price' => '599900',
                'g_sku_status' => GoodsSku::STATUS_ON_NOT_SALE,
                'g_sku_create_uid' => 1,
                'g_id' => $goods->g_id,
            ],
            [
                'g_sku_value' => '4:3;5:1',
                'g_sku_stock_num' => 100,
                'g_sku_price' => '678800',
                'g_sku_sale_price' => '698800',
                'g_sku_status' => GoodsSku::STATUS_ON_NOT_SALE,
                'g_sku_create_uid' => 1,
                'g_id' => $goods->g_id,
            ],
        ];
        $gModel = new GoodsModel();
        $skus = $gModel->createMultiGoodsSku($skuData, $goods);
        if(!$skus){
            $this->debug($gModel->getOneError());
        }
        console($skus);
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
