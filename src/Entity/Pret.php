<?php

namespace App\Entity;

use App\Repository\PretRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PretRepository::class)]

#[ApiResource(
        operations: [
            new GetCollection(normalizationContext: ['groups' => 'pret:read'],
            paginationEnabled: false,
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
        ),
        ],
    )]

#[ApiFilter(SearchFilter::class, properties: ['user.email' => 'exact'])]
class Pret
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['pret:read', 'produit:read', 'produitGet:read',  'carte:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['pret:read', 'carte:read'])] 
    private ?\DateTimeInterface $datePret = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['pret:read', 'carte:read'])]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['pret:read'])]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'prets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['pret:read', 'user:read', 'produit:read', 'produitGet:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'prets')]
    #[Groups(['pret:read'])]
    private ?Carte $carte = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\ManyToMany(targetEntity: Produit::class, inversedBy: 'prets')]
    #[Groups(['pret:read'])]
    private Collection $produit;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['pret:read', 'produit:read'])]
    private ?\DateTimeInterface $dateFinPrevue = null;

    /**
     * @var Collection<int, RelancePret>
     */
    #[ORM\OneToMany(mappedBy: 'pret', targetEntity: RelancePret::class)]
    #[Groups(['pret:read'])]
    private Collection $relancePrets;

    // construct

    public function __construct()
    {
        $this->datePret = new \DateTime();
        $this->produit = new ArrayCollection();
        $this->relancePrets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePret(): ?\DateTimeInterface
    {
        return $this->datePret;
    }

    public function setDatePret(\DateTimeInterface $datePret): static
    {
        $this->datePret = $datePret;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCarte(): ?Carte
    {
        return $this->carte;
    }

    public function setCarte(?Carte $carte): static
    {
        $this->carte = $carte;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduit(): Collection
    {
        return $this->produit;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produit->contains($produit)) {
            $this->produit->add($produit);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        $this->produit->removeElement($produit);

        return $this;
    }

    public function getDateFinPrevue(): ?\DateTimeInterface
    {
        return $this->dateFinPrevue;
    }

    public function setDateFinPrevue(?\DateTimeInterface $dateFinPrevue): static
    {
        $this->dateFinPrevue = $dateFinPrevue;

        return $this;
    }

    public function __toString(): string
    {
        return json_encode($this);
    }

    /**
     * @return Collection<int, RelancePret>
     */
    public function getRelancePrets(): Collection
    {
        return $this->relancePrets;
    }

    public function addRelancePret(RelancePret $relancePret): static
    {
        if (!$this->relancePrets->contains($relancePret)) {
            $this->relancePrets->add($relancePret);
            $relancePret->setPret($this);
        }

        return $this;
    }

    public function removeRelancePret(RelancePret $relancePret): static
    {
        if ($this->relancePrets->removeElement($relancePret)) {
            // set the owning side to null (unless already changed)
            if ($relancePret->getPret() === $this) {
                $relancePret->setPret(null);
            }
        }

        return $this;
    }
}
