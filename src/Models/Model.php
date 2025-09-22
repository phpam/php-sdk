<?php

namespace Phpam\Sdk\Models;

interface Model
{
    /**
     * Convert the Model to an array
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
