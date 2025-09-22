<?php

namespace Phpam\Sdk\Models\Meta;

use Phpam\Sdk\Models\Model;

class Auth implements Model
{
    private string $id;
    private string $username;
    private ?string $email = null;

    /**
     * @param string $id User ID or identifier
     * @param string $username Username or login name
     * @param string|null $email Email address (optional)
     */
    public function __construct(string $id, string $username, ?string $email = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
        ];
    }

}
