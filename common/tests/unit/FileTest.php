<?php
namespace common\tests;
use Yii;
use common\models\file\FileModel;


class FileTest extends \Codeception\Test\Unit
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
        return ;
        Yii::$app->db->beginTransaction();
        $fileData = [
            'file_is_private' => 0,
            'file_is_tmp' => 0,
            'file_save_name' => mt_rand(1111,9999) . '测试图片.jpg',
            'file_valid_time' => 3600,
            'file_save_type' => 'disk',
            'file_category' => 'test',
            'file_source_path' => '/home/master/tmp/raise.png'
        ];
        $fModel = new FileModel();
        $file = $fModel->createFileBySource($fileData);
        if(!$file){
            $this->debug($fModel->getOneError());
        }
        console($file->toArray());
    }
}
