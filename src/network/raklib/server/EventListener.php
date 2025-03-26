<?php
namespace blockcore\network\raklib\server;

use raklib\server\ServerEventListener;
use raklib\utils\InternetAddress;

class EventListener implements ServerEventListener {
    private \Logger $logger;
    private array $sessions = [];

    public function __construct(\Logger $logger) {
        $this->logger = $logger;
    }

    public function onClientConnect(int $sessionId, string $address, int $port, int $clientId): void {
        $this->sessions[$sessionId] = [
            'address' => $address,
            'port' => $port,
            'clientId' => $clientId,
            'connected' => true
        ];
        $this->logger->info("Nueva conexión: $address:$port (ID: $clientId)");
    }

    public function onClientDisconnect(int $sessionId, int $reason): void {
        if (isset($this->sessions[$sessionId])) {
            $session = $this->sessions[$sessionId];
            $this->logger->info("Desconexión: {$session['address']}:{$session['port']} (Razón: $reason)");
            unset($this->sessions[$sessionId]);
        }
    }

    public function onPacketReceive(int $sessionId, string $payload): void {
        $this->logger->debug("Paquete recibido", [
            'session' => $sessionId,
            'size' => strlen($payload),
            'hex' => bin2hex(substr($payload, 0, 4)) . '...'
        ]);
        
        // Aquí puedes decodificar el payload manualmente si es necesario
    }

    public function onRawPacketReceive(string $address, int $port, string $payload): void {
        $this->logger->debug("Paquete RAW", [
            'from' => "$address:$port",
            'size' => strlen($payload)
        ]);
    }

    public function onPacketAck(int $sessionId, int $identifierACK): void {
        $this->logger->debug("ACK recibido", [
            'session' => $sessionId,
            'ackId' => $identifierACK
        ]);
    }

    public function onBandwidthStatsUpdate(int $bytesSentDiff, int $bytesReceivedDiff): void {
        // Útil para monitoreo de red
        $this->logger->debug("Estadísticas BW", [
            'sent' => $this->formatBytes($bytesSentDiff),
            'received' => $this->formatBytes($bytesReceivedDiff)
        ]);
    }

    private function formatBytes(int $bytes): string {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function onPingMeasure(int $sessionId, int $pingMS): void {
        if (isset($this->sessions[$sessionId])) {
            $this->sessions[$sessionId]['ping'] = $pingMS;
            $this->logger->debug("Ping actualizado para sesión $sessionId: $pingMS ms");
        }
    }

    // Método adicional útil para obtener estadísticas
    public function getSessionInfo(int $sessionId): ?array {
        return $this->sessions[$sessionId] ?? null;
    }
}
