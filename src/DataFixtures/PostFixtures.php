<?php

namespace App\DataFixtures;

use App\Model\Post\Entity\Post;
use App\Model\Post\Entity\PostMeta;
use App\Model\Post\Entity\PostTranslation;
use App\Service\IdGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $translations = new ArrayCollection();

        $post = Post::create(
            IdGenerator::next(),
            new \DateTimeImmutable(),
            $translations,
            new PostMeta(
                'IP',
                'BROWSER'
            ),
            $this->getReference(UserFixtures::REFERENCE_USER)
        );
        $post->activate();

        $postTranslation = new PostTranslation();
        $postTranslation->setPost($post);
        $postTranslation->setTitle('test');
        $postTranslation->setContent('Test content');
        $postTranslation->setSlug('test');
        $postTranslation->setLanguageCode('en');

        $post->addTranslation($postTranslation);

        $manager->persist($post);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}