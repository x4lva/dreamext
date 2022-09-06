<?php

namespace App\DataFixtures;

use App\Model\User\Entity\User;
use App\Model\User\Entity\UserName;
use App\Model\User\Entity\UserRole;
use App\Model\User\Service\PasswordHasher;
use App\Service\IdGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const REFERENCE_ADMIN = 'user_user_admin';
    public const REFERENCE_USER = 'user_user_user';

    private $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $hash = $this->hasher->hash('password');

        $confirmed = $this->createSignUpConfirmedByEmail(
            new UserName('Brad', 'Pitt'),
            'user@gmail.com',
            $hash
        );
        $manager->persist($confirmed);
        $this->setReference(self::REFERENCE_USER, $confirmed);

        $admin = $this->createAdminByEmail(
            new UserName('James', 'Bond'),
            'admin@gmail.com',
            $hash
        );
        $manager->persist($admin);
        $this->setReference(self::REFERENCE_ADMIN, $admin);

        $manager->flush();
    }

    private function createAdminByEmail(UserName  $name, string $email, string $hash): User
    {
        $user = $this->createSignUpConfirmedByEmail($name, $email, $hash);
        $user->changeRole(UserRole::admin());
        return $user;
    }

    private function createSignUpConfirmedByEmail(UserName  $name, string $email, string $hash): User
    {
        $user = $this->createSignUpRequestedByEmail($name, $email, $hash);
        $user->confirmSignUp();
        return $user;
    }

    private function createSignUpRequestedByEmail(UserName $name, string $email, string $hash): User
    {
        return User::signUpByEmail(
            IdGenerator::next(),
            new \DateTimeImmutable(),
            $name,
            $email,
            $hash,
            'token'
        );
    }
}
