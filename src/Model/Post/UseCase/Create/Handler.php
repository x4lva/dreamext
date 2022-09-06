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
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Security;

class Handler
{
    private $posts;
    private $users;
    private $hasher;
    private $tokenizer;
    private $sender;
    private $em;

    public function __construct(
        PostRepository $posts,
        UserRepository $users,
        PasswordHasher $hasher,
        SignUpConfirmTokenizer $tokenizer,
        SignUpConfirmTokenSender $sender,
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
        $this->posts = $posts;
        $this->users = $users;
        $this->hasher = $hasher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get($command->user);

        $post = Post::create(
            IdGenerator::next(),
            new \DateTimeImmutable(),
            $command->translations,
            new PostMeta(
                $command->ip,
                $command->browser
            ),
            $user
        );

        foreach ($command->translations as $translation) {
            $translation->setPost($post);
        }
        $this->posts->add($post);

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new \DomainException('error.post.unique');
        }
    }
}