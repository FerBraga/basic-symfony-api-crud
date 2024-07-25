<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $adminUser = new User();
        $adminUser->setEmail('admin@meuemail.com');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword(
            $this->passwordHasher->hashPassword($adminUser, 'adminpassword')
        );
        $manager->persist($adminUser);

        $user = new User();
        $user->setEmail('user@meuemail.com');
        $user->setEmail('user@meuemail.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'userpassword')
        );
        $manager->persist($user);

        $manager->flush();
    }
}
