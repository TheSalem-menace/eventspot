<?php

namespace App\DataFixtures;

use App\Entity\Evenement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EvenementFixture extends Fixture implements DependentFixtureInterface
{
    public const EVENEMENT_REF = 'evenement-';

    private array $eventData = [
        ['Conférence Symfony 7 — Nouveautés et bonnes pratiques', 'conference', 150, 0.0],
        ['Atelier Machine Learning avec Python pour les débutants', 'atelier', 25, 49.99],
        ['Meetup Développeurs PHP — Partage d\'expériences', 'meetup', 80, 0.0],
        ['Formation Docker & Kubernetes en environnement de production', 'formation', 20, 199.99],
        ['Concert de musique électronique et jazz improvisé', 'concert', 500, 15.0],
        ['Conférence Intelligence Artificielle — Éthique et enjeux', 'conference', 300, 0.0],
        ['Atelier UX/UI Design — Prototypage avec Figma', 'atelier', 15, 79.99],
        ['Meetup Startups Tunisiennes — Pitch & Networking', 'meetup', 100, 0.0],
        ['Formation Symfony 7 complète — 2 jours intensifs', 'formation', 12, 349.99],
        ['Concert Piano — Grands classiques revisités', 'concert', 200, 25.0],
        ['Conférence Cloud & DevOps — Tendances 2026', 'conference', 250, 0.0],
        ['Atelier Introduction à React.js et TypeScript', 'atelier', 20, 59.99],
        ['Meetup Open Source — Contribuer aux projets communautaires', 'meetup', 60, 0.0],
        ['Formation Marketing Digital et SEO avancé', 'formation', 18, 149.99],
        ['Concert Gospel — Chœurs du monde', 'concert', 350, 20.0],
    ];

    public function getDependencies(): array
    {
        return [LieuFixtures::class, TagEvenementFixtures::class, UserFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $now = new \DateTime();

        foreach ($this->eventData as $i => $data) {
            $evenement = new Evenement();
            $evenement->setTitre($data[0]);
            $evenement->setCategorie($data[1]);
            $evenement->setCapaciteMax($data[2]);
            $evenement->setPrix($data[3]);

            // Description longue avec Faker
            $evenement->setDescription($faker->paragraphs(3, true) . ' Rejoignez-nous pour cet événement exceptionnel et enrichissez vos connaissances.');

            // Dates futures échelonnées
            $daysOffset = 10 + ($i * 8);
            $dateDebut = \DateTimeImmutable::createFromMutable((clone $now)->modify("+{$daysOffset} days"));
            $heureDebut = [9, 10, 14, 18, 19][array_rand([9, 10, 14, 18, 19])];
            $dateDebut = $dateDebut->setTime($heureDebut, 0);
            $evenement->setDateDebut($dateDebut);
            $evenement->setDateFin($dateDebut->modify('+4 hours'));

            // Lieu aléatoire parmi les 5
            $evenement->setLieu($this->getReference(LieuFixtures::LIEU_REF . ($i % 5)));

            // 1 ou 2 tags
            $evenement->addTag($this->getReference(TagEvenementFixtures::TAG_REF . ($i % 8)));
            if ($i % 3 === 0) {
                $evenement->addTag($this->getReference(TagEvenementFixtures::TAG_REF . (($i + 1) % 8)));
            }

            // Organisateur
            $orgaIndex = $i % 2;
            $evenement->setOrganisateur($this->getReference(UserFixtures::ORGA_REF . $orgaIndex));
            $evenement->setStatut('publie');

            $manager->persist($evenement);
            $this->addReference(self::EVENEMENT_REF . $i, $evenement);
        }

        $manager->flush();
    }
}
