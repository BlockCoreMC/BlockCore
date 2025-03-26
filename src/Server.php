<?php
namespace blockcore;

use blockcore\network\NetworkManager;
use blockcore\logger\BlockLogger;

class Server {

    private NetworkManager $networkManager;
    private \Logger $logger; 
    
    public function __construct() {
        $this->logger = new BlockLogger("BlockCore"); 
        $this->networkManager = new NetworkManager($this->logger);
        $this->logger->info("Servidor BlockCore inicializado");
    }

    public function start(): void {
        $this->logger->info("Iniciando servidor BlockCore");  
        $this->networkManager->start();
    }
}
