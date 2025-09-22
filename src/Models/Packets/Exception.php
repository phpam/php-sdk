<?php

namespace Phpam\Sdk\Models\Packets;

use Phpam\Sdk\Models\Model;

class Exception implements Model
{
    public string $message;

    public string $file;

    public int $line;
    private float $timestamp;

    /**
     * The stack trace of the exception
     *
     * @var string[]
     */
    public array $trace;

    public function __construct(
        \Throwable $exception
    ) {
        $this->message = $exception->getMessage();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->trace = explode("\n", $exception->getTraceAsString());
        $this->timestamp = microtime(true);
    }

    /**
     * Convert the Exception to an array
     */
    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'file' => $this->file,
            'line' => $this->line,
            'trace' => $this->trace,
            'timestamp' => $this->timestamp,
        ];
    }
}
