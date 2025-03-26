<?php
namespace blockcore\network\raklib\server;

use raklib\server\ProtocolAcceptor as RakLibProtocolAcceptor;

class ProtocolAcceptor implements RakLibProtocolAcceptor {
    
    private const SUPPORTED_VERSIONS = [11];

    public function accepts(int $protocolVersion): bool {
        return in_array($protocolVersion, self::SUPPORTED_VERSIONS, true);
    }

    public function getPrimaryVersion(): int {
        return end(self::SUPPORTED_VERSIONS); // Devuelve la versión más reciente
    }

    /**
     * Método adicional útil para debugging
     */
    public function getSupportedVersions(): array {
        return self::SUPPORTED_VERSIONS;
    }
}