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
use common\models\goods\query\GoodsSkuQuery;
use common\models\goods\ar\GoodsSource;
use common\models\file\FileModel;

class GSourceTest extends \Codeception\Test\Unit
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

    public function testCreate(){

        // return ;
        if(0){
            $fileData = [
                'file_is_private' => 0,
                'file_is_tmp' => 0,
                'file_save_name' => '玫瑰金.jpg',
                'file_valid_time' => 3600,
                'file_save_type' => 'disk',
                'file_category' => 'goods',
                'file_source_path' => '/home/kitralzhong/tmp/57d0d400Nfd249af4.jpg'
            ];
            $fileModel = new FileModel();
            $file = $fileModel->createFileBySource($fileData);
            if(!$file){
                $this->debug($fileModel->getOneError());
            }
        }


        // Yii::$app->db->beginTransaction();
        $object = 0 ? GoodsQuery::find()
                                ->where(['g_status' => Goods::STATUS_DRAFT])
                                ->one()
                      :
                      GoodsSkuQuery::findByValue('4:3;5:3;18:1')->one();


        $data = [
            'gs_type' => GoodsSource::TYPE_IMG,
            'gs_name' => '苹果iphone7',
            // 'gs_sid' => 'disk:goods/b73b07b70373e03ce268dddfe68ca76c.jpg',
            'gs_sid' => 'oss:goods/aa549a0dc0b8beb54250f9252e3e28f0.jpg'
        ];
        $gModel = new GoodsModel();
        $result = $gModel->createSource($data, $object);
        if(!$result){
            $this->debug($gModel->getOneError());
        }
        console($result->toArray());
    }
}
