<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Auteur;
use Faker\Factory;

class AuteurFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create("fr_FR");
        
        for($i = 0;$i <= 20; $i++){
            $auteur = new Auteur();
            $auteur->setNomPrenom($generator->name)
                   ->setSexe($generator->randomElement(['M','F']))
                   ->setDateNaissance($generator->dateTime($max = 'now', $timezone = null))
                   ->setNationalite($generator->country);
            
        $manager->persist($auteur);
        }

        $manager->flush();
    }
}
