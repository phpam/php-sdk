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
     * @var list<array<string, array<int, string>|int|string>> (this type is complex, see the constructStackTrace method for details)
     * TODO: improve the type here (da fuck is this)
     */
    public array $trace;


    /**
     * @param string $file
     * @param int $line
     * @param int $context
     * @return array<int, string> An array of lines of code surrounding the specified line
     */
    private function getSurroundingCode(
        string $file,
        int $line,
        int $context = 5
    ): array {
        $lines = file($file);

        if($lines === false) {
            return [];
        }


        $start = max($line - $context - 1, 0);
        $end = min($line + $context - 1, count($lines) - 1);
        $codeSnippet = [];
        for ($i = $start; $i <= $end; $i++) {
            $codeSnippet[$i + 1] = rtrim($lines[$i]);
        }
        return $codeSnippet;
    }

    private function constructStackTrace(\Throwable $exception): void
    {

        $entries = [];

        foreach ($exception->getTrace() as $trace) {
            $entry = [
                'file' => $trace['file'] ?? '[internal]',
                'line' => $trace['line'] ?? 0,
                'class' => $trace['class'] ?? '',
                'type' => $trace['type'] ?? '',
            ];
            if (isset($trace['file']) && isset($trace['line'])) {
                $entry['code'] = $this->getSurroundingCode($trace['file'], $trace['line']);
            }

            $entries[] = $entry;
        }

        $this->trace = $entries;
    }

    public function __construct(
        \Throwable $exception
    ) {
        $this->message = $exception->getMessage();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->constructStackTrace($exception);
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
