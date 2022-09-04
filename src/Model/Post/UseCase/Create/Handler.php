<?php

namespace App\Model\Post\UseCase\Create;

use App\Model\Post\Entity\Post;
use App\Model\Post\Entity\PostMeta;
use App\Model\Post\Entity\PostRepository;
use App\Model\User\Entity\UserRepository;
use App\Model\User\Service\PasswordHasher;
use App\Model\User\Service\SignUpConfirmTokenizer;
use App\Model\User\Service\SignUpConfirmTokenSender;
use App\Service\IdGenerator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class Handler
{
    private $posts;
    private $users;
    private $hasher;
    private $tokenizer;
    private $sender;
    private $em;
    private $security;

    public function __construct(
        PostRepository $posts,
        UserRepository $users,
        PasswordHasher $hasher,
        SignUpConfirmTokenizer $tokenizer,
        SignUpConfirmTokenSender $sender,
        EntityManagerInterface $em,
        Security $security
    )
    {
        $this->em = $em;
        $this->posts = $posts;
        $this->users = $users;
        $this->hasher = $hasher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->security = $security;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get($command->user);

        $post = Post::create(
            IdGenerator::next(),
            new \DateTimeImmutable(),
            $command->translations,
            new PostMeta(
                '123',
                '123'
            ),
            $user
        );

        foreach ($command->translations as $translation) {
            $translation->setPost($post);
        }
        $this->posts->add($post);

        try {
            $this->em->flush($post);
        } catch (UniqueConstraintViolationException $e) {
            throw new \DomainException('error.post.unique');
        }
    }
}