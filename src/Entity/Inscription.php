<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $dateInscription = null;

    #[ORM\Column(length: 15)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['confirmee', 'en_attente', 'annulee'])]
    private string $statut = 'confirmee';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 500)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(targetEntity: Evenement::class, inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Evenement $evenement = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?User $participant = null;

    public function getId(): ?int { return $this->id; }

    public function getDateInscription(): ?\DateTime { return $this->dateInscription; }
    public function setDateInscription(\DateTime $d): static { $this->dateInscription = $d; return $this; }

    #[ORM\PrePersist]
    public function setDateInscriptionValue(): void { $this->dateInscription = new \DateTime(); }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $s): static { $this->statut = $s; return $this; }

    public function getCommentaire(): ?string { return $this->commentaire; }
    public function setCommentaire(?string $c): static { $this->commentaire = $c; return $this; }

    public function getEvenement(): ?Evenement { return $this->evenement; }
    public function setEvenement(?Evenement $e): static { $this->evenement = $e; return $this; }

    public function getParticipant(): ?User { return $this->participant; }
    public function setParticipant(?User $u): static { $this->participant = $u; return $this; }
}
