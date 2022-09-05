<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Reset;

use App\Model\User\Entity\UserRepository;
use App\Model\User\Service\PasswordHasher;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $users;
    private $hasher;
    private $em;

    public function __construct(
        UserRepository $users,
        PasswordHasher $hasher,
        EntityManagerInterface $em
    )
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        if (!$user = $this->users->findByResetToken($command->token)) {
            throw new \DomainException('Incorrect or confirmed token.');
        }

        $user->passwordReset(
            new \DateTimeImmutable(),
            $this->hasher->hash($command->password)
        );

        $this->em->flush();
    }
}