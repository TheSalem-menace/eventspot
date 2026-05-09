<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    public function findUpcoming(int $limit = 6): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.dateDebut >= :now')
            ->andWhere('e.statut = :statut')
            ->setParameter('now', new \DateTime())
            ->setParameter('statut', 'publie')
            ->orderBy('e.dateDebut', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByFiltersQuery(?string $titre = null, ?string $categorie = null, ?string $ville = null, ?int $tagId = null): Query
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.lieu', 'l')
            ->leftJoin('e.tags', 't')
            ->andWhere('e.statut = :statut')
            ->setParameter('statut', 'publie')
            ->orderBy('e.dateDebut', 'ASC');

        if ($titre) {
            $qb->andWhere('e.titre LIKE :titre')
               ->setParameter('titre', '%' . $titre . '%');
        }
        if ($categorie) {
            $qb->andWhere('e.categorie = :categorie')
               ->setParameter('categorie', $categorie);
        }
        if ($ville) {
            $qb->andWhere('l.ville LIKE :ville')
               ->setParameter('ville', '%' . $ville . '%');
        }
        if ($tagId) {
            $qb->andWhere('t.id = :tagId')
               ->setParameter('tagId', $tagId);
        }

        return $qb->getQuery();
    }

    // Keep array version for backward-compat
    public function findByFilters(?string $titre = null, ?string $categorie = null, ?string $ville = null, ?int $tagId = null): array
    {
        return $this->findByFiltersQuery($titre, $categorie, $ville, $tagId)->getResult();
    }
}
