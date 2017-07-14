<?php use kshopapi\tests\ApiTester;
$I = new ApiTester($scenario);
$I->wantTo('上传文件是否成功，并且正确访问数据');
$I->haveHttpHeader('Content-Type', 'multipart/form-data');
$fileData = [
    'file_is_private' => 0,
    'file_is_tmp' => 1,
    'file_save_name' => '测试图片.jpg',
    'file_valid_time' => 3600,
    'file_save_type' => 'oss',
    'file_category' => 'test',
];
$I->sendPOST('/file/create', $fileData, [
    'file' => codecept_data_dir('test.jpg')
]);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContains("file_query_id");


$fileRes = json_decode($I->grabResponse(), true);
$fileUrl = $fileRes['data']['file_url'];
$fileMd5Value = $fileRes['data']['file_md5_value'];
$I->sendGet($fileUrl);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeBinaryResponseEquals($fileMd5Value);
