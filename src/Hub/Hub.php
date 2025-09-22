<?php

namespace Phpam\Sdk\Hub;

use Phpam\Sdk\Models\Meta\Auth;
use Phpam\Sdk\Models\Meta\Project;
use Phpam\Sdk\Models\Meta\Request;
use Phpam\Sdk\Models\Packets\Packet;
use Phpam\Sdk\Models\Packets\Trace;
use Phpam\Sdk\Transport\Transport;
use Phpam\Sdk\Transport\Transporters;

class Hub
{
    private Transporters $transporter;
    public Packet $packet;

    private string $endpoint;
    /**
     * @var array <string, string> Custom headers to include in the request
     */
    private array $headers = [];

    /**
     * @var Trace[] an array of Trace objects representing the trace stack
     */
    private array $traces = [];

    /**
     * @param Transporters $transporter The transport method to use
     * @param string $endpoint The endpoint URL to send data to
     * @param array<string, string> $headers Custom headers to include in the request
     */
    public function __construct(
        Transporters $transporter = Transporters::CURL,
        string $endpoint = '',
        array $headers = [],
    ) {
        $this->transporter = $transporter;
        $this->endpoint = $endpoint;
        $this->headers = $headers;

        $this->packet = new Packet(
            trace: new \Phpam\Sdk\Models\Packets\Trace(
                operation: 'request.start',
            ),
            request: Request::generate()
        );
    }

    //@section Meta


    /**
     * Add Auth information to the packet
     * @param Auth $auth
     * @return $this
     */
    public function withAuth(
        Auth $auth
    ): self {
        $this->packet->auth = $auth;
        return $this;
    }


    /**
     * Add tags to the packet
     * @param array<string, string> $tags
     * @return $this
     */
    public function withTags(
        array $tags
    ): self {
        $this->packet->tags = array_merge($this->packet->tags, $tags);
        return $this;
    }

    /**
     * Add Project information to the packet
     * @param Project $project The project to associate with this packet
     * @return $this
     */
    public function withProject(
        Project $project
    ): self {
        $this->packet->project = $project;


        $projectAuth = $project->getAuth();
        $this->headers['x-pam-client-id'] = $projectAuth['client_id'];
        $this->headers['x-pam-client-secret'] = $projectAuth['client_secret'];

        return $this;

    }

    //@section Traces
    /**
     * Add a Trace to the trace stack
     * @param Trace $trace
     * @return void
     */
    public function attachChild(Trace $trace): void
    {
        $this->getCurrentTrace()?->attachChild($trace);
        $this->traces[] = $trace;
    }

    public function finishChild()
    {
        $this->popTrace()?->finished();
    }

    /**
     * Retrieve the current Trace from the top of the stack
     * @return Trace|null
     */
    public function getCurrentTrace(): ?Trace
    {
        return end($this->traces) ?: null;
    }

    /**
     * Remove and return the current Trace from the top of the stack
     * @return Trace|null
     */
    public function popTrace(): ?Trace
    {
        return array_pop($this->traces) ?: null;
    }

    //@section Exceptions
    public function throwException(
        \Throwable $throwable
    ): void {
        $this->packet->exception = new \Phpam\Sdk\Models\Packets\Exception($throwable);
        $this->packet->trace->finished();
        $this->send();
    }

    //@section Sending
    public function send(): bool
    {
        $targetClass = $this->transporter->value;
        /** @var Transport $transporter */
        $transporter = new $targetClass();

        if (!$transporter->isAvailable()) {
            return false;
        }

        return $transporter->send(
            $this->endpoint,
            $this->packet,
            $this->headers
        );
    }
}
