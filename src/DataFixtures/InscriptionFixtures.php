<?php

namespace App\DataFixtures;

use App\Entity\Inscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InscriptionFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [EvenementFixture::class, UserFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $statuts = ['confirmee', 'confirmee', 'confirmee', 'en_attente', 'annulee'];
        $count = 0;

        // Each of the 5 regular users subscribes to ~6 events = 30 inscriptions
        for ($userId = 0; $userId < 5; $userId++) {
            $user = $this->getReference(UserFixtures::USER_REF . $userId);
            $eventIds = range(0, 14);
            shuffle($eventIds);
            $eventIds = array_slice($eventIds, 0, 6);

            foreach ($eventIds as $eventId) {
                $inscription = new Inscription();
                $inscription->setEvenement($this->getReference(EvenementFixture::EVENEMENT_REF . $eventId));
                $inscription->setParticipant($user);
                $inscription->setStatut($statuts[$count % 5]);
                $inscription->setCommentaire($count % 3 === 0 ? 'Très impatient de participer à cet événement !' : null);
                $manager->persist($inscription);
                $count++;
            }
        }

        $manager->flush();
    }
}
