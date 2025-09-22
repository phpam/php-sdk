<?php

namespace Phpam\Sdk\Models\Packets;

use Phpam\Sdk\Models\Meta\Auth;
use Phpam\Sdk\Models\Meta\Project;
use Phpam\Sdk\Models\Meta\Request;
use Phpam\Sdk\Models\Model;

class Packet implements Model
{
    public Trace $trace;
    public ?Exception $exception = null;
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
     * @param Exception|null $exception The exception information (if any)
     */
    public function __construct(
        Trace $trace,
        ?Exception $exception = null,
        ?Auth $auth = null,
        ?Request $request = null,
        array $tags = []
    ) {
        $this->trace = $trace;
        $this->exception = $exception;
        $this->auth = $auth;
        $this->request = $request;
        $this->tags = $tags;
    }

    public function toArray(): array
    {
        return [
            'trace' => $this->trace->toArray(),
            'exception' => $this->exception?->toArray(),
            'auth' => $this->auth?->toArray(),
            'request' => $this->request?->toArray(),
            'project' => $this->project?->toArray(),
            'tags' => $this->tags,
        ];
    }
}
