<?php

namespace App\DataFixtures;

use App\Entity\Profession;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfessionFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $profession1 = new Profession();
        $profession2 = new Profession();
        $profession3 = new Profession();
        $profession4 = new Profession();
        $profession5 = new Profession();
        $profession6 = new Profession();
        $profession7 = new Profession();
        $profession8 = new Profession();

        $profession1->setName('Informatique');
        $profession2->setName('Mathématiques');
        $profession3->setName('Médécine');
        $profession4->setName('Journalisme');
        $profession5->setName('Réseau');
        $profession6->setName('Politique');
        $profession7->setName('Agriculture');
        $profession8->setName('Pêche');

        $manager->persist($profession1);
        $manager->persist($profession2);
        $manager->persist($profession3);
        $manager->persist($profession4);
        $manager->persist($profession5);
        $manager->persist($profession6);
        $manager->persist($profession7);
        $manager->persist($profession8);

        $manager->flush();
    }
}
