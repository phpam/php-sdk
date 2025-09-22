<?php

namespace Phpam\Sdk\Models\Packets;

use Phpam\Sdk\Models\Model;

class Trace implements Model
{
    private string $operation;
    private float $started_at;

    private float $finished_at = -1;
    private float $memory_usage;


    /**
     * The children of this Trace
     *
     * @var Trace[]
     */
    private array $children;

    public function __construct(
        string $operation,
    ) {
        $this->operation = $operation;
        $this->started_at = microtime(true);
        $this->memory_usage = memory_get_usage(true);
    }

    /**
     * Finish the current Trace
     *
     * @return $this
     */
    public function finished(): self
    {
        $this->finished_at = microtime(true);
        $memory_after = memory_get_usage(true);
        // Always keep the highest memory usage (before or after as the trace)
        if ($memory_after > $this->memory_usage) {
            $this->memory_usage = $memory_after;
        }

        return $this;
    }

    /**
     * Add a child Trace to this Trace
     */
    public function attachChild(Trace $child): void
    {
        $this->children[] = $child;
    }

    /**
     * Get the duration of this Trace in milliseconds
     */
    public function getDuration(): float
    {
        return ($this->finished_at - $this->started_at) * 1000;
    }

    /**
     * Convert the Trace to an array
     */
    public function toArray(): array
    {
        return [
            'operation' => $this->operation,
            'started_at' => (int)$this->started_at,
            'finished_at' => (int) $this->finished_at,
            'duration' => (int) $this->getDuration(),
            'memory_usage' => (int) $this->memory_usage,
            // Recursively convert children to arrays (if any)
            'children' => array_map(fn ($child) => $child->toArray(), $this->children ?? []),
        ];
    }
}
