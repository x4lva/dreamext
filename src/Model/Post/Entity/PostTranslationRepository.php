<?php

declare(strict_types=1);

namespace App\Model\Post\Entity;

use App\ReadModel\User\Filter\Filter;
use App\Service\LocaleHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Knp\Component\Pager\Pagination\PaginationInterface;

class PostTranslationRepository
{
    private $em;
    private $repo;
    private $localeHelper;

    public function __construct(EntityManagerInterface $em, LocaleHelper $localeHelper)
    {
        $this->em = $em;
        $this->localeHelper = $localeHelper;
        $this->repo = $em->getRepository(PostTranslation::class);
    }

    public function get(string $id): PostTranslation
    {
        /** @var PostTranslation $postTranslation */
        if (!$postTranslation = $this->repo->find($id)) {
            throw new EntityNotFoundException('PostTranslation is not found.');
        }
        return $postTranslation;
    }

    public function getByLocaleAndSlug(string $locale, string $slug): ?PostTranslation
    {
        $postTranslation = $this->repo->findOneBy(['slug' => $slug]);

        if (!$postTranslation) {
            return null;
        }

        if ($postTranslation->getLanguageCode() === $locale) {
            return $postTranslation;
        }

        $post = $postTranslation->getPost();
        $postTranslation = $post->getTranslations()->filter(function (PostTranslation $translation) use ($locale) {
            return $translation->getLanguageCode() === $locale;
        })->first();

        if ($postTranslation) {
            return $postTranslation;
        }

        return $post->getTranslations()->first();
    }

}