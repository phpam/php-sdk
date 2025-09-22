<?php

namespace Phpam\Sdk\Transport;

use Phpam\Sdk\Models\Packets\Packet;

interface Transport
{
    /**
     * Check if the transport method is available in the current environment (e.g., required extensions are loaded)
     * @return bool True if the transport method is available, false otherwise
     */
    public function isAvailable(): bool;

    /**
     * @param string $endpoint
     * @param Packet $data
     * @param array<string,string> $headers
     * @return bool True on success, false on failure
     */
    public function send(
        string $endpoint,
        Packet $data,
        array $headers = []
    ): bool;
}
