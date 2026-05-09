<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture
{
    public const LIEU_REF = 'lieu-';

    private array $lieux = [
        ['Palais des Congrès Paris', '2 Place de la Porte Maillot', 'Paris', 3700],
        ['Cité Internationale Lyon', '33 Quai Charles de Gaulle', 'Lyon', 1200],
        ['Halle Tony Garnier', '20 Place Antonin Perrin', 'Lyon', 1000],
        ['Zénith de Bordeaux', '5 Avenue du Peuple Belge', 'Bordeaux', 900],
        ['Palais des Arts Marseille', '9 Rue de la Caisserie', 'Marseille', 800],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach ($this->lieux as $i => $data) {
            $lieu = new Lieu();
            $lieu->setNom($data[0]);
            $lieu->setAdresse($data[1]);
            $lieu->setVille($data[2]);
            $lieu->setCapacite($data[3]);
            $manager->persist($lieu);
            $this->addReference(self::LIEU_REF . $i, $lieu);
        }
        $manager->flush();
    }
}
