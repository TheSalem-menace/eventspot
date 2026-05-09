<?php

namespace App\DataFixtures;

use App\Entity\TagEvenement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagEvenementFixtures extends Fixture
{
    public const TAG_REF = 'tag-';

    private array $tags = [
        ['Technologie', '#007bff'],
        ['Formation', '#28a745'],
        ['Réseau', '#ffc107'],
        ['Design', '#e83e8c'],
        ['Marketing', '#fd7e14'],
        ['Open Source', '#6f42c1'],
        ['Intelligence Artificielle', '#20c997'],
        ['Startups', '#dc3545'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach ($this->tags as $i => $data) {
            $tag = new TagEvenement();
            $tag->setNom($data[0]);
            $tag->setCouleur($data[1]);
            $manager->persist($tag);
            $this->addReference(self::TAG_REF . $i, $tag);
        }
        $manager->flush();
    }
}
