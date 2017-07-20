<?php use kshopapi\tests\ApiTester;
$I = new ApiTester($scenario);
$I->wantTo('测试分片任务的创建');


$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$data = [
    'file_save_type' => "disk",
    'file_save_name' => "中国地图一亿像素.jpg",
    'timestamp' => '1500538759',
    'file_is_private' => 0,
    'file_is_tmp' => 0,
    'access_token' => 'abc',
];
$I->sendPOST('/file/chunk-task-create', $data);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContains("file_task_id");
