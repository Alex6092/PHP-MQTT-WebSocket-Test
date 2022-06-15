<?php
    require "vendor/autoload.php"; 

    use \cboden\ratchet\app;
    use PhpMqtt\Client\Exceptions\MqttClientException;
    use PhpMqtt\Client\MqttClient;
    require "class/ChatServer.php";


    $chatServer = new ChatServer();

    $server = '192.168.64.198';
    $port = 1883;
    $clientId = 'test-publisher';
    $client = new MqttClient($server, $port, $clientId);
    $client->connect();
    $client->publish('topic/test', 'Hello broker !', 0);
    $client->subscribe('topic/sub', function($topic, $message) use ($chatServer) {
        echo("Topic $topic : $message\n");
        $chatServer->diffuseMessage($message);
    }, 0);

    $loop = React\EventLoop\Factory::create();
    $loop->addPeriodicTimer(0.05, function() use($client) {
        $client->loopOnce(microtime(true));
    });

    $app = new Ratchet\App('127.0.0.1', 8080, '127.0.0.1', $loop);
    $app->route('/chat', $chatServer, ['*']);
    $app->run();

?>