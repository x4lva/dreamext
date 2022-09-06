<?php

declare(strict_types=1);

namespace App\Model\Post\UseCase\Activate;

class Command
{
    /**
     * @var string
     */
    public $id;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }
}