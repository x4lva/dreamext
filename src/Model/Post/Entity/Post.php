<?php

declare(strict_types=1);

namespace App\Model\Post\Entity;

use App\Model\User\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="post")
 */
class Post
{
    public const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $date;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE", nullable=true)
     */
    private $user;

    /**
     * @ORM\Embedded(class="PostMeta")
     */
    private $meta;

    /**
     * @var PostTranslation[]|Collection
     * @ORM\OneToMany(targetEntity="App\Model\Post\Entity\PostTranslation", mappedBy="post", orphanRemoval=true, cascade={"persist"})
     */
    private $translations;

    public function __construct(string $id, \DateTimeImmutable $date)
    {
        $this->id = $id;
        $this->date = $date;
        $this->status = Post::STATUS_ACTIVE;
        $this->translations = new ArrayCollection();
    }

    public static function create(string $id, \DateTimeImmutable $date, Collection $translations, PostMeta $postMeta, User $user)
    {
        $post = new self($id, $date);

        $post->status = Post::STATUS_WAIT;
        $post->translations = $translations;
        $post->meta = $postMeta;
        $post->user = $user;

        return $post;
    }

    public function edit(Collection $translations): void
    {
        foreach ($translations as $translation) {
            $this->addTranslation($translation);
        }

        $this->status = self::STATUS_WAIT;
    }

    public function activate(): void
    {
        $this->status = self::STATUS_ACTIVE;
    }

    public function addTranslation(PostTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setPost($this);
        }

        return $this;
    }

    public function removeTranslation(PostTranslation $translation): self
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);

            if ($translation->getPost() === $this) {
                $translation->setPost(null);
            }
        }

        return $this;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getMeta(): PostMeta
    {
        return $this->meta;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
    public function getTitle(?string $languageCode = 'en'): ?string
    {
        $translation = $this->translations->filter(function (PostTranslation $translation) use ($languageCode) {
            return $translation->getLanguageCode() === $languageCode;
        })->first();

        return $translation ? $translation->getTitle() : $this->translations->first()->getTitle();
    }

    public function getContent(?string $languageCode = 'en'): ?string
    {
        $translation = $this->translations->filter(function (PostTranslation $translation) use ($languageCode) {
            return $translation->getLanguageCode() === $languageCode;
        })->first();

        return $translation ? $translation->getContent() : $this->translations->first()->getContent();
    }

    public function getSlug(?string $languageCode = 'en'): ?string
    {
        $translation = $this->translations->filter(function (PostTranslation $translation) use ($languageCode) {
            return $translation->getLanguageCode() === $languageCode;
        })->first();

        return $translation ? $translation->getSlug() : $this->translations->first()->getSlug();
    }
}