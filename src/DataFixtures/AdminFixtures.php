<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager)
    {
        $admin2 = new Admin();
        $admin2->setEmail('admin@admin.com');
        $admin2->setCreatedAt(new \DateTime());
        $admin2->setPassword($this->passwordHasher->hashPassword($admin2, 'admin'));
        $admin2->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin2);
        $manager->flush();
    }
}