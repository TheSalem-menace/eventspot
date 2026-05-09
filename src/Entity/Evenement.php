<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => ['event:read']],
    denormalizationContext: ['groups' => ['event:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: "is_granted('ROLE_ORGANISATEUR')"),
        new Put(security: "is_granted('ROLE_ORGANISATEUR')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ]
)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    #[Groups(['event:read', 'event:write'])]
    private string $titre;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 30)]
    #[Groups(['event:read', 'event:write'])]
    private ?string $description;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Groups(['event:read', 'event:write'])]
    private ?\DateTimeImmutable $dateDebut;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Groups(['event:read', 'event:write'])]
    private ?\DateTimeImmutable $dateFin;

    #[ORM\ManyToOne(inversedBy: 'capaciteMax')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['event:read', 'event:write'])]
    private ?Lieu $lieu = null;

    #[ORM\Column]
    #[Assert\Range(min: 1)]
    #[Groups(['event:read', 'event:write'])]
    private int $capaciteMax;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    #[Groups(['event:read', 'event:write'])]
    private ?float $prix = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['conference', 'atelier', 'meetup', 'formation', 'concert'])]
    #[Groups(['event:read', 'event:write'])]
    private string $categorie;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['brouillon', 'publie', 'complet', 'annule'])]
    #[Groups(['event:read', 'event:write'])]
    private string $statut;

    #[ORM\Column]
    #[Groups(['event:read'])]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private ?string $imageName = null;

    #[ORM\ManyToOne(inversedBy: 'evenementsOrganises')]
    #[Groups(['event:read'])]
    private ?User $organisateur = null;

    #[ORM\ManyToMany(targetEntity: TagEvenement::class, inversedBy: 'evenements')]
    #[Groups(['event:read', 'event:write'])]
    private Collection $tags;

    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Inscription::class, orphanRemoval: true)]
    private Collection $inscriptions;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setDateCreationValue(): void
    {
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): static { $this->titre = $titre; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }
    public function getDateDebut(): ?\DateTimeImmutable { return $this->dateDebut; }
    public function setDateDebut(\DateTimeImmutable $dateDebut): static { $this->dateDebut = $dateDebut; return $this; }
    public function getDateFin(): ?\DateTimeImmutable { return $this->dateFin; }
    public function setDateFin(\DateTimeImmutable $dateFin): static { $this->dateFin = $dateFin; return $this; }
    public function getLieu(): ?Lieu { return $this->lieu; }
    public function setLieu(?Lieu $lieu): static { $this->lieu = $lieu; return $this; }
    public function getCapaciteMax(): ?int { return $this->capaciteMax; }
    public function setCapaciteMax(int $capaciteMax): static { $this->capaciteMax = $capaciteMax; return $this; }
    public function getPrix(): ?float { return $this->prix; }
    public function setPrix(float $prix): static { $this->prix = $prix; return $this; }
    public function getCategorie(): ?string { return $this->categorie; }
    public function setCategorie(string $categorie): static { $this->categorie = $categorie; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }
    public function getDateCreation(): ?\DateTime { return $this->dateCreation; }
    public function setDateCreation(\DateTime $dateCreation): static { $this->dateCreation = $dateCreation; return $this; }
    public function getImageName(): ?string { return $this->imageName; }
    public function setImageName(?string $imageName): static { $this->imageName = $imageName; return $this; }
    public function getOrganisateur(): ?User { return $this->organisateur; }
    public function setOrganisateur(?User $organisateur): static { $this->organisateur = $organisateur; return $this; }

    public function getTags(): Collection { return $this->tags; }
    public function addTag(TagEvenement $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
        return $this;
    }
    public function removeTag(TagEvenement $tag): static
    {
        $this->tags->removeElement($tag);
        return $this;
    }

    public function getInscriptions(): Collection { return $this->inscriptions; }
    public function addInscription(Inscription $inscription): static
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setEvenement($this);
        }
        return $this;
    }
    public function removeInscription(Inscription $inscription): static
    {
        if ($this->inscriptions->removeElement($inscription)) {
            if ($inscription->getEvenement() === $this) {
                $inscription->setEvenement(null);
            }
        }
        return $this;
    }

    public function getNbInscrits(): int
    {
        return $this->inscriptions->filter(fn($i) => $i->getStatut() === 'confirmee')->count();
    }

    public function getPlacesRestantes(): int
    {
        return max(0, $this->capaciteMax - $this->getNbInscrits());
    }

    public function getTauxRemplissage(): float
    {
        if ($this->capaciteMax === 0) return 0;
        return min(100, round(($this->getNbInscrits() / $this->capaciteMax) * 100, 1));
    }

    public function __toString(): string
    {
        return $this->titre ?? '';
    }
}
