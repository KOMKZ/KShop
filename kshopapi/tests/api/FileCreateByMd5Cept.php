<?php use kshopapi\tests\ApiTester;
$I = new ApiTester($scenario);
$I->wantTo('测试传入文件的md5值和相关参数实现上传');

// 不传文件流，传md5,检测是否服务器是否有该文件
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$fileData['file_md5_value'] = '30e1a411ece0c0feb0df36043ea27ae4';
$fileData['file_is_private'] = 1;
$fileData['file_is_tmp'] = 0;
$fileData['file_save_name'] = "测试图片2.jpg";
$fileData['file_category'] = 'test';
$I->sendPOST('/file/create', $fileData);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContains("file_query_id");
