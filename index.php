<?php

error_reporting(E_ALL & ~E_DEPRECATED);

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Lucien\WsPhp\ChatService;

require __DIR__ . '/vendor/autoload.php';

$port = 8080;

$server = IoServer::factory(
  new HttpServer(
    new WsServer(
      new ChatService()
    )
  ),
  $port
);

echo "Elindult a szerver a $port számú porton.\n";
$server->run();