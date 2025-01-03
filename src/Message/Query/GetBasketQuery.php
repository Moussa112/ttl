<?php

declare(strict_types=1);

namespace App\Message\Query;

use Ramsey\Uuid\UuidInterface;

class GetBasketQuery
{
    public function __construct(private UuidInterface $userId)
    {
    }

    public function getUserId(): UuidInterface
    {
        return $this->userId;
    }
}
