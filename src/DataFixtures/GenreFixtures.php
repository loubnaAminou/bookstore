<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Genre;
use Faker\Factory;

class GenreFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create("fr_FR");
        for($i = 0;$i <= 10; $i++){
            $genre = new Genre();
            $genre->setNom($generator->word);
            
        $manager->persist($genre);
        }

        $manager->flush();
    }
}
