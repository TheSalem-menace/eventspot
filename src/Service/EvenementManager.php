<?php

namespace App\Service;

use App\Entity\Evenement;
use App\Entity\User;
use App\Repository\InscriptionRepository;
use App\Repository\EvenementRepository;

class EvenementManager
{
    public function __construct(
        private InscriptionRepository $inscRepo,
        private EvenementRepository $eventRepo
    ) {}

    public function getPlacesRestantes(Evenement $e): int
    {
        $inscrits = $this->getNbInscrits($e);
        return max(0, $e->getCapaciteMax() - $inscrits);
    }

    public function estInscrit(User $u, Evenement $e): bool
    {
        return $this->inscRepo->findOneBy([
            'evenement' => $e,
            'participant' => $u,
        ]) !== null;
    }

    public function getNbInscrits(Evenement $e): int
    {
        return $this->inscRepo->count([
            'evenement' => $e,
            'statut' => 'confirmee',
        ]);
    }

    public function getEvenementsParCategorie(): array
    {
        $result = [];
        foreach ($this->eventRepo->findAll() as $ev) {
            $result[$ev->getCategorie()][] = $ev;
        }
        return $result;
    }
}
