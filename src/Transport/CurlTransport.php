<?php

namespace Phpam\Sdk\Transport;

use Phpam\Sdk\Models\Packets\Packet;
use Phpam\Sdk\Models\Packets\Trace;

class CurlTransport implements Transport
{
    /**
     * Check if curl is available
     * @return bool True if curl is available, false otherwise
     */
    public function isAvailable(): bool
    {
        return function_exists('curl_init');
    }

    /**
     * @inheritDoc
     * Send the Trace data to the endpoint using curl
     */
    public function send(string $endpoint, Packet $data, array $headers = []): bool
    {
        $json = json_encode($data->toArray());
        if ($json === false) {
            return false;
        }

        $headers = array_merge($headers, [
            'Content-Length: ' . strlen($json),
            'Content-Type: application/json',
            'Accept: application/json',
        ]);

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        //@phpstan-ignore-next-line argument.type it will be an <string,string> array but curl_setopt doesn't have proper types
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }
}
