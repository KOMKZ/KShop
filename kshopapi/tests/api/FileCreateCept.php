<?php use kshopapi\tests\ApiTester;
$I = new ApiTester($scenario);
$I->wantTo('upload a file');
$I->haveHttpHeader('Content-Type', 'multipart/form-data');
$fileData = [
    'is_private' => 1,
    'is_tmp' => 1,
    'save_name' => '测试图片.jpg',
    'valid_time' => 3600,
    'save_type' => 'disk',
    'category' => 'test',
];
$I->sendPOST('/file/create', $fileData, [
    'file' => codecept_data_dir('test.jpg')
]);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'code' => 0,
    'data' => [
        'is_private' => 1,
        'is_tmp' => 0,
        'save_name' => '测试图片.jpg',
    ]
]);
