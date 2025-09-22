<?php

namespace Phpam\Sdk\Models\Meta;

use Phpam\Sdk\Models\Model;

class Project implements Model
{
    private string $name;
    private ?string $version;
    private ?string $environment = null;

    private string $clientId = '';
    private string $clientSecret = '';

    /**
     * @param string $name Project name
     * @param string|null $version Project version
     * @param string|null $environment Project environment (optional)
     */
    public function __construct(string $name, string $version = null, ?string $environment = null)
    {
        $this->name = $name;
        $this->version = $version;
        $this->environment = $environment;

        if (!$version) {
            $this->tryGetVersionFromGit();
        }
    }

    public function withAuth(
        string $clientId,
        string $clientSecret
    ): self {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        return $this;
    }


    public function tryGetVersionFromGit(): void
    {
        $gitVersion = shell_exec('git describe --tags --abbrev=0 2>/dev/null');

        if ($gitVersion === false || $gitVersion === null) {
            $this->version = 'unknown';
            return;
        }

        $gitVersion = trim($gitVersion);
        if ($gitVersion) {
            $this->version = $gitVersion;
            return;
        }

        $this->version = 'unknown';
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'version' => $this->version,
            'environment' => $this->environment,
        ];
    }

    /**
     * @return array<string, string> Auth information as an associative array
     */
    public function getAuth()
    {
        return [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];

    }

}
