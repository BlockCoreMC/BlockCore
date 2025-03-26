<?php
namespace blockcore\network;

use raklib\server\Server;
use raklib\server\ServerSocket;
use raklib\utils\InternetAddress;
use raklib\utils\ExceptionTraceCleaner;
use blockcore\logger\BlockLogger;
use blockcore\network\raklib\server\{EventSource,EventListener,ProtocolAcceptor};

class NetworkManager {
    private Server $server;
    private \Logger $logger;

    public function __construct(\Logger $logger)
    {
        $this->logger = $logger;
        $this->logger->info('NetworkManager inicializado');
    }

    public function start(string $ip = "0.0.0.0", int $port = 19132): void {
        try {
            $socket = new ServerSocket(new InternetAddress($ip, $port, 4));
            $eventSource = new EventSource($this->logger);
            $eventListener = new EventListener($this->logger);
            $mainPath = dirname(__DIR__, 2);
            $exceptionCleaner = new ExceptionTraceCleaner($mainPath);
            
            $this->server = new Server(
                mt_rand(0, PHP_INT_MAX),
                $this->logger,
                $socket,
                1492,
                new ProtocolAcceptor(),
                $eventSource,
                $eventListener,
                $exceptionCleaner
            );

            $this->logger->info("Servidor iniciado en $ip:$port");
            
            while(true) {
                $this->server->tickProcessor();
                usleep(20_000);
            }
        } catch(\Throwable $e) {
            $this->logger->error("Error: " . $e->getMessage());
        }
    }
}
