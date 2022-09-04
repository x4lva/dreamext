<?php

declare(strict_types=1);

namespace App\Model\Post\UseCase\Edit;

use App\Model\Post\Entity\Post;
use App\Model\Post\Entity\PostTranslation;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $id;

    /**
     * @var PostTranslation[]|ArrayCollection
     * @Assert\NotBlank()
     */
    public $translations;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromPost(Post $post): self
    {
        $command = new self($post->getId());
        $command->translations = $post->getTranslations();

        return $command;
    }
}