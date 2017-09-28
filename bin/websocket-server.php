<?php

require_once __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\Factory;
use React\Stomp\Client;
use Thruway\Peer\Router;
use Thruway\Transport\RatchetTransportProvider;
use Thruway\Transport\RawSocketTransportProvider;
use Workshop\EventHandler;
use React\Stomp\Factory as StompFactory;

/** The loop is shared between multiple objects so we all use the same loop in different objects for timers etc **/
$loop = Factory::create();

/** React has a STOMP library for communicating with RabbitMQ (with it's STOMP plugin) within an event loop (async) **/
$stompFactory = new StompFactory($loop);
$client       = $stompFactory->createClient(
    [
        'host'     => 'rabbitmq',
        'port'     => 61613,
        'login'    => 'rabbitmq',
        'passcode' => 'rabbitmq',
        'vhost'    => '/'
    ]
);

$router       = new Router($loop);
$eventHandler = new EventHandler('our-namespace', $loop);

/**
 * @todo STOMP with RabbitMQ specifies the queue name must be prefixed with something before an external queue name
 *       (it's in the docs)
 *       Here, you should connect with the STOMP client. Then(), subscribe and the callback should be
 *       a function in your event handler
* // **/
$client->connect()->then(function (Client $client) use ($eventHandler) {
    // @todo Subscribe to RabbitMq (need to read the docs to figure out the name of the queue to use!!)
    //       Use callback for event handler (client) method, for example, 'onRabbitMQMessage'
});

/** The port we're handling websockets over **/
$router->addTransportProvider(new RatchetTransportProvider('0.0.0.0', 1338));

/** The port we're handling raw socket connections over **/
$router->registerModule(new RawSocketTransportProvider('0.0.0.0', 1339));

/** Add a Client to the router so we can register RPC endpoints **/
$router->addInternalClient($eventHandler);

$router->start();