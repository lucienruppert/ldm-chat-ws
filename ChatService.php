<?php

namespace Lucien\WsPhp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require __DIR__ . '/vendor/autoload.php';

class ChatService implements MessageComponentInterface
{

  protected $clients;
  protected $lastMessage;

  public function __construct()
  {
    echo "Chat szolgáltatás elindítva!\n";
    $this->clients = new \SplObjectStorage();
    $this->lastMessage;
  }

  public function onOpen(ConnectionInterface $connection)
  {
    echo "Új kapcsolat megnyitva: $connection->resourceId\n";
    $this->clients->attach($connection);
  }

  public function onMessage(ConnectionInterface $fromClient, $message)
  {
    echo "Bejövő üzenet: kliens => $fromClient->resourceId, üzenet => $message\n"; 
    $this->lastMessage = ['timestamp' => time(), 'client' => $fromClient->resourceId, 'message' => $message];
    foreach ($this->clients as $client) {
      if ($fromClient != $client) {
        $client->send(json_encode(['type' => 'lastMessage', 'data' => $this->lastMessage]));
        echo "Üzenet továbbítva: kliens => $client->resourceId, üzenet => $message\n";
      }
    }
  }

  public function onClose(ConnectionInterface $connection)
  {
    $this->clients->detach($connection);
    echo "A kapcsolat megszakítva a $connection->resourceId klienssel!\n";
  }

  public function onError(ConnectionInterface $connection, \Exception $error)
  {
    echo "Hiba történt: {$error->getMessage()}\n";
    $connection->close();
  } 

}
