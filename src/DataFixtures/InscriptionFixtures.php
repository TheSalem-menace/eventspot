<?php

namespace App\DataFixtures;

use App\Entity\Inscription;
use App\Entity\Evenement;
use App\Entity\User;
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

        // Get all events and users from database
        $events = $manager->getRepository(Evenement::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();
        
        $regularUsers = array_filter($users, function($user) {
            $roles = $user->getRoles();
            return in_array('ROLE_USER', $roles) && !in_array('ROLE_ADMIN', $roles) && !in_array('ROLE_ORGANISATEUR', $roles);
        });

        // Each regular user subscribes to ~6 events = 30 inscriptions
        foreach ($regularUsers as $user) {
            $eventIds = array_keys($events);
            shuffle($eventIds);
            $eventIds = array_slice($eventIds, 0, 6);

            foreach ($eventIds as $eventId) {
                if (isset($events[$eventId])) {
                    $inscription = new Inscription();
                    $inscription->setEvenement($events[$eventId]);
                    $inscription->setParticipant($user);
                    $inscription->setStatut($statuts[$count % 5]);
                    $inscription->setCommentaire($count % 3 === 0 ? 'Très impatient de participer à cet événement !' : null);
                    $inscription->setDateInscription(new \DateTime());
                    $manager->persist($inscription);
                    $count++;
                }
            }
        }

        $manager->flush();
    }
}
