<?php

declare(strict_types=1);

namespace App\Service;

use Ramsey\Uuid\Uuid;

class IdGenerator
{
    public static function next(): string
    {
        return Uuid::uuid4()->toString();
    }
}