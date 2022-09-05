<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\ResetToken;
use Ramsey\Uuid\Uuid;

class ResetTokenizer
{
    public function generate(): ResetToken
    {
        return new ResetToken(
            Uuid::uuid4()->toString(),
            (new \DateTimeImmutable())->add(new \DateInterval('PT1H'))
        );
    }
}