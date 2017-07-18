<?php use kshopapi\tests\ApiTester;
$I = new ApiTester($scenario);
$I->wantTo('测试分片任务的创建');


$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$data['file_task_data'] = [

];
$I->sendPOST('/file/chunk-task-create', $data);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContains("file_task_id");
