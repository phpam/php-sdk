<?php

namespace Phpam\Sdk\Models\Meta;

use Phpam\Sdk\Models\Model;

class Request implements Model
{
    private string $method;
    private string $url;

    /**
     * @var array<string, string> $headers Request headers
     */
    private array $headers;
    private ?string $body = null;

    /**
     * @param string $method HTTP method (e.g., GET, POST)
     * @param string $url Request URL
     * @param array<string, string> $headers Request headers
     * @param string|null $body Request body (optional)
     */
    public function __construct(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null
    ) {
        $this->method = $method;
        $this->url = $url;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'method' => $this->method,
            'url' => $this->url,
            'headers' => $this->headers,
            'body' => $this->body,
        ];
    }


    /**
     * Get data from the $_SERVER superglobal safely
     * @param string $key The key to retrieve from $_SERVER
     * @param mixed $default The default value if the key does not exist
     * @return mixed The value from $_SERVER or the default

     */
    private static function getDataFromServerObject(
        string $key,
        $default = null
    ): mixed {
        return $_SERVER[$key] ?? $default;
    }

    /**
     * @return Request
     */
    public static function generate(): Request
    {
        $body = file_get_contents('php://input') ?: null;

        $method = (string) self::getDataFromServerObject('REQUEST_METHOD', 'CLI');
        $url = (string) self::getDataFromServerObject('REQUEST_URI', 'CLI');
        $headers = getallheaders() ?: [];

        // Ensure headers are array<string, string>
        $headers = array_map('strval', $headers);

        return new Request(
            $method,
            $url,
            $headers,
            $body
        );
    }
}
