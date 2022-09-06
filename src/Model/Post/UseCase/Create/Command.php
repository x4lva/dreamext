<?php

declare(strict_types=1);

namespace App\Model\Post\UseCase\Create;

use App\Model\Post\Entity\PostTranslation;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $user;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $ip;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $browser;

    /**
     * @var PostTranslation[]|ArrayCollection
     * @Assert\NotBlank()
     */
    public $translations;

    public function __construct(string $user, $ip, $browser)
    {
        $this->user = $user;
        $this->ip = $ip;
        $this->browser = $browser;
        $this->translations = new ArrayCollection();
    }

    public function addTranslation(PostTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
        }

        return $this;
    }

    public function removeTranslation(PostTranslation $translation): self
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
        }

        return $this;
    }
}