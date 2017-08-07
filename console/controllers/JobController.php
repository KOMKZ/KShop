<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\base\Worker;
use PhpAmqpLib\Connection\AMQPStreamConnection;


/**
 *
 */
class JobController extends Controller{

    public $d = null;
    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['d']
        );
    }

    public function actionKill(){
        Worker::$pidFile = Yii::getAlias('@app/runtime/logs/pid.txt');
        Worker::$action = 'kill';
        Worker::runAll();
    }

    public function actionReload(){
        Worker::$pidFile = Yii::getAlias('@app/runtime/logs/pid.txt');
        Worker::$action = 'reload';
        Worker::runAll();
    }

    public function actionStop(){
        Worker::$pidFile = Yii::getAlias('@app/runtime/logs/pid.txt');
        Worker::$action = 'stop';
        Worker::runAll();
    }

    public function actionStatus(){
        Worker::$pidFile = Yii::getAlias('@app/runtime/logs/pid.txt');
        Worker::$action = 'status';
        Worker::runAll();
    }

    public function actionStart(){
        // $this->runEmail();
        // $this->runFile();
        Worker::$action = 'start';
        if(true === $this->d){
            Worker::$daemonize = true;
        }
        Worker::runAll();
    }
    // public function actionRunFile(){
    //     $this->runFile();
    //     Worker::$action = 'start';
    //     Worker::runAll();
    // }
    // public function actionRunEmail(){
    //     $this->runEmail();
    //     Worker::$action = 'start';
    //     Worker::runAll();
    // }
    // private function runEmail(){
    //     $worker = new Worker("tcp://127.0.0.1:2345");
    //     $worker->name = 'email-worker';
    //     $worker::$logFile = Yii::getAlias('@app/runtime/logs/ewlog.txt');
    //     $worker::$pidFile = Yii::getAlias('@app/runtime/logs/pid.txt');
    //     $worker->count = EmailWorker::$emailWorkerCount;
    //     $worker->onWorkerStart = function($worker)
    //     {
    //         $connection = new AMQPStreamConnection('localhost', 5672, 'kitral', 'philips');
    //         $channel = $connection->channel();
    //         $channel->queue_declare('email-job', false, true, false, false);
    //         $channel->basic_qos(null, 1, null);
    //         $channel->basic_consume('email-job', '', false, false, false, false, ['\common\models\email\EmailWorker', 'handleEmail']);
    //         while(count($channel->callbacks)) {
    //             $channel->wait();
    //         }
    //         $channel->close();
    //         $connection->close();
    //     };
    // }
    // private function runFile(){
    //     $worker = new Worker("tcp://127.0.0.1:2346");
    //     $worker->name = 'file-worker';
    //     $worker::$logFile = Yii::getAlias('@app/runtime/logs/fwlog.txt');
    //     $worker::$pidFile = Yii::getAlias('@app/runtime/logs/pid.txt');
    //     $worker->count = FileWorker::$workerCount;
    //     $worker->onWorkerStart = function($worker)
    //     {
    //         $connection = new AMQPStreamConnection('localhost', 5672, 'kitral', 'philips');
    //         $channel = $connection->channel();
    //         $channel->queue_declare('file-job', false, true, false, false);
    //         $channel->basic_qos(null, 1, null);
    //         $channel->basic_consume('file-job', '', false, false, false, false, ['\common\models\file\FileWorker', 'handleFile']);
    //         while(count($channel->callbacks)) {
    //             $channel->wait();
    //         }
    //         $channel->close();
    //         $connection->close();
    //     };
    // }



}
