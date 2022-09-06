<?php

declare(strict_types=1);

namespace App\Model\Post\UseCase\Activate;

use App\Model\Post\Entity\PostRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $posts;
    private $em;

    public function __construct(
        PostRepository $posts,
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
        $this->posts = $posts;
    }
    public function handle(Command $command) {
        $post = $this->posts->get($command->id);

        $post->activate();

        $this->em->flush();
    }
}