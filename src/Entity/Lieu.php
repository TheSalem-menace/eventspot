<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LieuRepository::class)]
#[ApiResource]
class Lieu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    #[Groups(['event:read', 'event:write'])]
    private string $nom;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['event:read', 'event:write'])]
    private string $adresse;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups(['event:read', 'event:write'])]
    private string $ville;

    #[ORM\Column]
    #[Assert\Range(min: 1)]
    #[Groups(['event:read', 'event:write'])]
    private int $capacite;

    #[ORM\OneToMany(targetEntity: Evenement::class, mappedBy: 'lieu')]
    private Collection $capaciteMax;

    public function __construct()
    {
        $this->capaciteMax = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(string $adresse): static { $this->adresse = $adresse; return $this; }
    public function getVille(): ?string { return $this->ville; }
    public function setVille(string $ville): static { $this->ville = $ville; return $this; }
    public function getCapacite(): ?int { return $this->capacite; }
    public function setCapacite(int $capacite): static { $this->capacite = $capacite; return $this; }

    public function getCapaciteMax(): Collection { return $this->capaciteMax; }

    public function __toString(): string { return $this->nom ?? ''; }
}
