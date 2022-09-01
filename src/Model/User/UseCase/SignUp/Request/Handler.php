<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\User\Entity\User;
use App\Model\User\Entity\UserName;
use App\Model\User\Entity\UserRepository;
use App\Model\User\Service\PasswordHasher;
use App\Model\User\Service\SignUpConfirmTokenizer;
use App\Model\User\Service\SignUpConfirmTokenSender;
use App\Service\IdGenerator;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $users;
    private $hasher;
    private $tokenizer;
    private $sender;
    private $em;

    public function __construct(
        UserRepository $users,
        PasswordHasher $hasher,
        SignUpConfirmTokenizer $tokenizer,
        SignUpConfirmTokenSender $sender,
        EntityManagerInterface $em
    )
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $email = $command->email;

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User already exists.');
        }

        $user = User::signUpByEmail(
            IdGenerator::next(),
            new \DateTimeImmutable(),
            new UserName(
                $command->firstName,
                $command->lastName
            ),
            $email,
            $this->hasher->hash($command->password),
            $token = $this->tokenizer->generate()
        );

        $this->users->add($user);

        $this->em->flush();

        $this->sender->send($email, $token);
    }
}