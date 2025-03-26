<?php
namespace blockcore\network\raklib\server;

use raklib\server\{ServerInterface, ServerEventSource};

class EventSource implements ServerEventSource {
    private \Logger $logger;

    public function __construct(\Logger $logger) {
        $this->logger = $logger;
    }

    public function process(ServerInterface $server): bool {
        try {
            $this->logger->debug("Procesando eventos del servidor");
            
            $server->tickProcessor();
            
            return true;
        } catch (\Throwable $e) {
            $this->logger->error("Error en EventSource: " . $e->getMessage());
            return false;
        }
    }
}