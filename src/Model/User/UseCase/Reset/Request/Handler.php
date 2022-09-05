<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Request;

use App\Model\User\Entity\UserRepository;
use App\Model\User\Service\ResetTokenizer;
use App\Model\User\Service\ResetTokenSender;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $users;
    private $tokenizer;
    private $sender;
    private $em;

    public function __construct(
        UserRepository $users,
        ResetTokenizer $tokenizer,
        ResetTokenSender $sender,
        EntityManagerInterface $em
    )
    {
        $this->users = $users;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->getByEmail($command->email);

        $user->requestPasswordReset(
            $this->tokenizer->generate(),
            new \DateTimeImmutable()
        );

        $this->em->flush();

        $this->sender->send($user->getEmail(), $user->getResetToken());
    }
}