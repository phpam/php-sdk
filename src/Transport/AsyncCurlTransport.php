<?php

namespace Phpam\Sdk\Transport;

use Phpam\Sdk\Models\Packets\Packet;
use Phpam\Sdk\Models\Packets\Trace;

class AsyncCurlTransport implements Transport
{
    /**
     * Check if the transport method is available in the current environment (e.g., required extensions are loaded)
     * @return bool
     */
    public function isAvailable(): bool
    {
        // check if the shell_exec function is available and not disabled
        if (!function_exists('shell_exec')) {
            return false;
        }

        $disabledFunctions = ini_get('disable_functions');
        if ($disabledFunctions) {
            $disabledFunctionsArray = array_map('trim', explode(',', $disabledFunctions));
            if (in_array('shell_exec', $disabledFunctionsArray, true)) {
                return false;
            }
        }


        // check if curl is installed
        $output = shell_exec('curl --version');
        if ($output === null) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     * Send the Trace data to the endpoint using curl in an asynchronous way (using shell_exec)
     */
    public function send(
        string $endpoint,
        Packet $data,
        array $headers = []
    ): bool {

        $command = 'curl -X POST ' . escapeshellarg($endpoint);

        $json = json_encode($data->toArray());
        if ($json === false) {
            return false;
        }

        // Default headers for JSON payload
        $headers['Content-Length'] = strlen($json);
        $headers['Content-Type'] = 'application/json';
        $headers['Accept'] = 'application/json';


        foreach ($headers as $key => $value) {
            $header = $key . ': ' . $value;
            $command .= ' -H ' . escapeshellarg($header);
        }

        $command .= ' -d ' . escapeshellarg($json);
        $command .= ' > /dev/null 2>&1 &'; // run in background
        shell_exec($command);

        return true; // we assume it worked, as we can't get the result of the async call
    }


}
