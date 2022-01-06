<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Livre;
use Faker\Factory;
use App\Entity\Auteur;
use App\Entity\Genre;

class LivreFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create("fr_FR");
        
        for($i = 0;$i <= 50; $i++){
            $livre = new Livre();
            $livre->setIsbn($generator->regexify('[0-9]{3}-[0-9]{1}-[0-9]{4}-[0-9]{4}-[0-9]{1}'))
                   ->setTitre($generator->sentence($nbWords = 4, $variableNbWords = true))
                   ->setNombrePages($generator->numberBetween($min = 10, $max = 500))
                   ->setDateParution($generator->dateTimeBetween($startDate = '-121 years', $endDate = 'now', $timezone = null) )
                   ->setNote($generator->numberBetween($min = 0, $max = 20));
            
            for($j = 0; $j < mt_rand(1,3); $j++){
                $genre = new Genre();
                $genre->setNom($generator->word)
                      ->addLivre($livre);
                $livre->addGenre($genre);
            
            $manager->persist($genre);
            }

            for($k = 0; $k < mt_rand(1,3); $k++){
                $auteur = new Auteur();
                $auteur->setNomPrenom($generator->name)
                   ->setSexe($generator->randomElement(['M','F']))
                   ->setDateNaissance($generator->dateTime($max = 'now', $timezone = null))
                   ->setNationalite($generator->country)
                   ->addLivre($livre);

                $livre->addAuteur($auteur);
                $manager->persist($auteur);
            }
        $manager->persist($livre);
        }
        $manager->flush();
    }
}
