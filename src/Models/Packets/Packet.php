<?php

namespace Phpam\Sdk\Models\Packets;

use Phpam\Sdk\Models\Meta\Auth;
use Phpam\Sdk\Models\Meta\Project;
use Phpam\Sdk\Models\Meta\Request;
use Phpam\Sdk\Models\Model;
use Phpam\Sdk\Models\Packets\Exception;

class Packet implements Model
{
    public Trace $trace;
    /**
     * @var Exception[] $exceptions An array of Exception objects
     */
    public array $exceptions = [];
    public ?Auth $auth;
    public ?Request $request = null;
    public ?Project $project = null;
    /**
     * @var array<string,string> $tags Additional tags or metadata
     */
    public array $tags = [];


    /**
     * Create a new Packet
     * @param Trace $trace The trace information
     * @param Request|null $request The request information (if any)
     * @param Auth|null $auth The authenticated user information (if any)
     * @param array<string,string> $tags Additional tags or metadata
     */
    public function __construct(
        Trace $trace,
        ?Auth $auth = null,
        ?Request $request = null,
        array $tags = []
    ) {
        $this->trace = $trace;
        $this->auth = $auth;
        $this->request = $request;
        $this->tags = $tags;
    }

    public function addException(
        Exception $exception
    ): self {
        $this->exceptions[] = $exception;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'trace' => $this->trace->toArray(),
            'exception' => $this->exceptions ? array_map(fn ($e) => $e->toArray(), $this->exceptions) : null,
            'auth' => $this->auth?->toArray(),
            'request' => $this->request?->toArray(),
            'project' => $this->project?->toArray(),
            'tags' => $this->tags,
        ];
    }
}
