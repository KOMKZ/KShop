<?php
namespace common\base;

use Workerman\Autoloader;
use Workerman\Lib\Timer;
use Workerman\Worker as BaseWorker;
use Workerman\Events\EventInterface;

/**
 *
 */
 class Worker extends BaseWorker
 {
     static public $action = null;
     public static function runAll()
     {
         self::checkSapiEnv();
         self::init();
         static::parseCommand();
         self::daemonize();
         self::initWorkers();
         self::installSignal();
         self::saveMasterPid();
         self::forkWorkers();
         self::displayUI();
         self::resetStd();
         self::monitorWorkers();
     }
     protected static function parseCommand()
     {
         global $argv;
         // Check argv;
         $start_file = $argv[0];
         if (null === self::$action) {
             exit("Usage: php yourfile.php {start|stop|restart|reload|status|kill}\n");
         }
         // Get command.
         $command  = trim(self::$action);
         $command2 = self::$daemonize ? '-d' : null;

         // Start command.
         $mode = '';
         if ($command === 'start') {
             if ($command2 === '-d') {
                 $mode = 'in DAEMON mode';
             } else {
                 $mode = 'in DEBUG mode';
             }
         }
         self::log("Workerman[$start_file] $command $mode");

         // Get master process PID.
         $master_pid      = @file_get_contents(self::$pidFile);
         $master_is_alive = $master_pid && @posix_kill($master_pid, 0);
         // Master is still alive?
         if ($master_is_alive) {
             if ($command === 'start') {
                 self::log("Workerman[$start_file] already running");
                 exit;
             }
         } elseif ($command !== 'start' && $command !== 'restart' && $command !== 'kill') {
             self::log("Workerman[$start_file] not run");
             exit;
         }
         // execute command.

         switch ($command) {
             case 'kill':
                 exec("ps aux | grep $start_file | grep -v grep | awk '{print $2}' |xargs kill -SIGINT");
                 usleep(100000);
                 exec("ps aux | grep $start_file | grep -v grep | awk '{print $2}' |xargs kill -SIGKILL");
                 break;
             case 'start':
                 if ($command2 === '-d') {
                     Worker::$daemonize = true;
                 }
                 break;
             case 'status':
                 if (is_file(self::$_statisticsFile)) {
                     @unlink(self::$_statisticsFile);
                 }
                 // Master process will send status signal to all child processes.
                 posix_kill($master_pid, SIGUSR2);
                 // Waiting amoment.
                 usleep(100000);
                 // Display statisitcs data from a disk file.
                 @readfile(self::$_statisticsFile);
                 exit(0);
             case 'restart':
             case 'stop':
                 self::log("Workerman[$start_file] is stoping ...");
                 // Send stop signal to master process.
                 $master_pid && posix_kill($master_pid, SIGINT);
                 // Timeout.
                 $timeout    = 5;
                 $start_time = time();
                 // Check master process is still alive?
                 while (1) {
                     $master_is_alive = $master_pid && posix_kill($master_pid, 0);
                     if ($master_is_alive) {
                         // Timeout?
                         if (time() - $start_time >= $timeout) {
                             self::log("Workerman[$start_file] stop fail");
                             exit;
                         }
                         // Waiting amoment.
                         usleep(10000);
                         continue;
                     }
                     // Stop success.
                     self::log("Workerman[$start_file] stop success");
                     if ($command === 'stop') {
                         exit(0);
                     }
                     if ($command2 === '-d') {
                         Worker::$daemonize = true;
                     }
                     break;
                 }
                 break;
             case 'reload':
                 posix_kill($master_pid, SIGUSR1);
                 self::log("Workerman[$start_file] reload");
                 exit;
             default :
                 exit("Usage: php yourfile.php {start|stop|restart|reload|status|kill}\n");
         }
     }
 }
