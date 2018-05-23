<?php
return [
    'amqpConn' => new PhpAmqpLib\Connection\AMQPStreamConnection(
        'localhost',
        '5672',
        'guest',
        'guest'
    )
];
