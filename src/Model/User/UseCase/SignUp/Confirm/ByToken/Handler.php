<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Confirm\ByToken;

use App\Model\User\Entity\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $users;
    private $em;

    public function __construct(UserRepository $users, EntityManagerInterface $em)
    {
        $this->users = $users;
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        if (!$user = $this->users->findByConfirmToken($command->token)) {
            throw new \DomainException('Incorrect or confirmed token.');
        }

        $user->confirmSignUp();

        $this->em->flush();
    }
}