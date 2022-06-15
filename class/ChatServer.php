<?php

class ChatServer implements Ratchet\MessageComponentInterface 
{
    private $clients;

    public function __construct() 
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(Ratchet\ConnectionInterface $conn) 
    {
        echo("Client connected\n");
        $this->clients->attach($conn);
    }

    public function onMessage(Ratchet\ConnectionInterface $from, $message) 
    {
        echo("Message received : $message\n");
        foreach ($this->clients as $client) {
            if ($client !== $from) {
                $client->send($message);
            }
        }
    }

    public function diffuseMessage($message)
    {
        echo("Send message $message to websocket clients\n");
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }

    public function onClose(Ratchet\ConnectionInterface $conn) 
    {
        echo("Client disconnected\n");
        $this->clients->detach($conn);
    }

    public function onError(Ratchet\ConnectionInterface $conn, \Exception $e) 
    {
        $conn->close();
        echo("Error: {$e->getMessage()}\n");
    }
}

?>