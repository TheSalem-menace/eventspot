<?php

namespace App\Tests\Service;

use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Entity\User;
use App\Repository\EvenementRepository;
use App\Repository\InscriptionRepository;
use App\Service\EvenementManager;
use PHPUnit\Framework\TestCase;

class EvenementManagerTest extends TestCase
{
    private function makeEvenement(int $capacite): Evenement
    {
        $e = new Evenement();
        $e->setTitre('Test Event Title Pour Test Unitaire');
        $e->setDescription('Une description suffisamment longue pour passer la validation de trente caractères minimum requis.');
        $e->setCapaciteMax($capacite);
        $e->setPrix(0);
        $e->setCategorie('conference');
        $e->setStatut('publie');
        $e->setDateDebut(new \DateTimeImmutable('+1 day'));
        $e->setDateFin(new \DateTimeImmutable('+1 day +4 hours'));
        return $e;
    }

    public function testGetPlacesRestantes(): void
    {
        $inscRepo = $this->createMock(InscriptionRepository::class);
        $inscRepo->method('count')->willReturn(3);
        $eventRepo = $this->createMock(EvenementRepository::class);

        $manager = new EvenementManager($inscRepo, $eventRepo);
        $evenement = $this->makeEvenement(10);

        $this->assertEquals(7, $manager->getPlacesRestantes($evenement));
    }

    public function testGetPlacesRestantesNeverNegative(): void
    {
        $inscRepo = $this->createMock(InscriptionRepository::class);
        $inscRepo->method('count')->willReturn(20);
        $eventRepo = $this->createMock(EvenementRepository::class);

        $manager = new EvenementManager($inscRepo, $eventRepo);
        $evenement = $this->makeEvenement(10);

        $this->assertEquals(0, $manager->getPlacesRestantes($evenement));
    }

    public function testEstInscritWhenTrue(): void
    {
        $inscription = new Inscription();
        $inscRepo = $this->createMock(InscriptionRepository::class);
        $inscRepo->method('findOneBy')->willReturn($inscription);
        $eventRepo = $this->createMock(EvenementRepository::class);

        $manager = new EvenementManager($inscRepo, $eventRepo);
        $user = new User();
        $evenement = $this->makeEvenement(10);

        $this->assertTrue($manager->estInscrit($user, $evenement));
    }

    public function testEstInscritWhenFalse(): void
    {
        $inscRepo = $this->createMock(InscriptionRepository::class);
        $inscRepo->method('findOneBy')->willReturn(null);
        $eventRepo = $this->createMock(EvenementRepository::class);

        $manager = new EvenementManager($inscRepo, $eventRepo);
        $user = new User();
        $evenement = $this->makeEvenement(10);

        $this->assertFalse($manager->estInscrit($user, $evenement));
    }

    public function testGetNbInscrits(): void
    {
        $inscRepo = $this->createMock(InscriptionRepository::class);
        $inscRepo->method('count')->willReturn(5);
        $eventRepo = $this->createMock(EvenementRepository::class);

        $manager = new EvenementManager($inscRepo, $eventRepo);
        $evenement = $this->makeEvenement(10);

        $this->assertEquals(5, $manager->getNbInscrits($evenement));
    }
}
