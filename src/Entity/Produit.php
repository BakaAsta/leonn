<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Get;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ApiResource(
    operations: [
        // autoriser la personne a write dans les produit
        new Get(normalizationContext: ['groups' => ['produitGet:read']]),
        new GetCollection(
            normalizationContext: ['groups' => ['produit:read']],
            paginationEnabled: false,
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN')",
        ),
        // recherche de produit par id 
        new Delete(
            security: "is_granted('ROLE_ADMIN')" // Optionnel, selon votre configuration de sécurité
        ), 
    ],
)]

class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['produit:read', 'produitGet:read', 'pret:read', 'categorie:read', 'typeProduit:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['produit:read', 'produitGet:read', 'pret:read', 'categorie:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Groups(['produit:read', 'produitGet:read', 'pret:read', 'categorie:read'])]
    private ?string $refInterne = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['produit:read', 'produitGet:read', 'pret:read', 'categorie:read'])]
    private ?string $refFabricant = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['produit:read', 'produitGet:read'])]
    private ?string $commentaire = null;

    // #[ORM\Column(nullable: true)]
    // #[Groups(['produit:read', 'produitGet:read'])]
    // private ?bool $rebus = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['produit:read', 'produitGet:read', 'pret:read'])]
    private ?\DateTimeInterface $dateRebus = null;

    #[ORM\Column]
    #[Groups(['produit:read', 'produitGet:read'])]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column]
    #[Groups(['produit:read', 'produitGet:read'])]
    private ?\DateTime $updatedAt = null;

    /**
     * @var Collection<int, TypeProduit>
     */
    #[ORM\ManyToMany(targetEntity: TypeProduit::class, inversedBy: 'produits')]
    #[Groups(['produit:read', 'produitGet:read', 'pret:read'])]
    private Collection $typeProduit;

    /**
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'produits')]
    #[Groups(['produit:read', 'produitGet:read', 'pret:read'])]
    private Collection $categories;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[Groups(['produit:read', 'produitGet:read', 'pret:read'])]
    private ?Marque $marque = null;

    /**
     * @var Collection<int, Pret>
     */
    #[ORM\ManyToMany(targetEntity: Pret::class, mappedBy: 'produit')]
    #[Groups(['produit:read', 'produitGet:read'])]
    private Collection $prets;

    #[ORM\Column]
    #[Groups(['produit:read', 'produitGet:read', 'pret:read'])]
    private ?int $quantite = null;

    #[ORM\Column(length: 255)]
    #[Groups(['produit:read', 'produitGet:read', 'pret:read'])]
    private ?string $etat = null;
   

    public function __construct()
    {
        $this->createAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTime();
        $this->typeProduit = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->prets = new ArrayCollection();
        $this->etat = 'Disponible';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getRefInterne(): ?string
    {
        return $this->refInterne;
    }

    public function setRefInterne(?string $refInterne): static
    {
        $this->refInterne = $refInterne;

        return $this;
    }

    public function getRefFabricant(): ?string
    {
        return $this->refFabricant;
    }

    public function setRefFabricant(?string $refFabricant): static
    {
        $this->refFabricant = $refFabricant;

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

    public function getDateRebus(): ?\DateTimeInterface
    {
        return $this->dateRebus;
    }

    public function setDateRebus(?\DateTimeInterface $dateRebus): static
    {
        $this->dateRebus = $dateRebus;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, TypeProduit>
     */
    public function getTypeProduit(): Collection
    {
        return $this->typeProduit;
    }

    public function addTypeProduit(TypeProduit $typeProduit): static
    {
        if (!$this->typeProduit->contains($typeProduit)) {
            $this->typeProduit->add($typeProduit);
        }

        return $this;
    }

    public function removeTypeProduit(TypeProduit $typeProduit): static
    {
        $this->typeProduit->removeElement($typeProduit);

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(?Marque $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * @return Collection<int, Pret>
     */
    public function getPrets(): Collection
    {
        return $this->prets;
    }

    public function addPret(Pret $pret): static
    {
        if (!$this->prets->contains($pret)) {
            $this->prets->add($pret);
            $pret->addProduit($this);
        }

        return $this;
    }

    public function removePret(Pret $pret): static
    {
        if ($this->prets->removeElement($pret)) {
            $pret->removeProduit($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

   
}
