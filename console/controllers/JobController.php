<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\base\Worker;
use common\models\set\SetModel;
use PhpAmqpLib\Connection\AMQPStreamConnection;


/**
 *
 */
class JobController extends Controller{

    public $d = null;

    public $pidFile = '@app/runtime/logs/pid.txt';

    public $logFile = '@app/runtime/logs/ewlog.txt';

    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['d']
        );
    }



    public function actionKill(){
        Worker::$pidFile = Yii::getAlias($this->pidFile);
        Worker::$logFile = Yii::getAlias($this->logFile);
        Worker::$action = 'kill';
        Worker::runAll();
    }

    public function actionReload(){
        Worker::$pidFile = Yii::getAlias($this->pidFile);
        Worker::$logFile = Yii::getAlias($this->logFile);
        Worker::$action = 'reload';
        Worker::runAll();
    }

    public function actionStop(){
        Worker::$pidFile = Yii::getAlias($this->pidFile);
        Worker::$logFile = Yii::getAlias($this->logFile);
        Worker::$action = 'stop';
        Worker::runAll();
    }

    public function actionStatus(){
        Worker::$pidFile = Yii::getAlias($this->pidFile);
        Worker::$logFile = Yii::getAlias($this->logFile);
        Worker::$action = 'status';
        Worker::runAll();
    }

    public function actionStart(){
        $this->runEmail();
        Worker::$action = 'start';
        if(true === (boolean)$this->d){
            Worker::$daemonize = true;
        }
        Worker::runAll();
    }


    private function runEmail(){
        $worker = new Worker("tcp://127.0.0.1:2345");
        $worker->name = 'email-worker';
        $worker::$logFile = Yii::getAlias($this->logFile);
        $worker::$pidFile = Yii::getAlias($this->pidFile);
        $worker->count = SetModel::get('worker.email_worker_count');
        $worker->onWorkerStart = function($worker)
        {
            $connection = new AMQPStreamConnection(
                SetModel::get('amqp.host'),
                SetModel::get('amqp.port'),
                SetModel::get('amqp.user'),
                SetModel::get('amqp.pwd'));
            $channel = $connection->channel();
            $channel->queue_declare('email-job', false, true, false, false);
            $channel->basic_qos(null, 1, null);
            $channel->basic_consume('email-job', '', false, false, false, false, ['\common\models\mail\EmailWorker', 'handleEmail']);
            while(count($channel->callbacks)) {
                $channel->wait();
            }
            $channel->close();
            $connection->close();
        };
    }




}
