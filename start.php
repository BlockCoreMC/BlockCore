<?php
require __DIR__ . '/vendor/autoload.php';

use blockcore\Server;

// 2. Lo inyectas al servidor
$server = new Server();

// 3. Inicias el servidor
$server->start();