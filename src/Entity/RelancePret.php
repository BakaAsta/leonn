<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Repository\RelancePretRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RelancePretRepository::class)]
#[ApiResource(
    operations: [
        // autoriser la personne a write dans les produit
        new Post(
            security: "is_granted('ROLE_ADMIN')",
        ),
    ],
)]
class RelancePret
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    // pret:read
    #[Groups(['pret:read'])]
    private ?int $id = null;
    
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['pret:read'])]
    private ?string $contenu = null;

    #[ORM\ManyToOne(inversedBy: 'relancePrets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pret $pret = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['pret:read'])]
    private ?string $titre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getPret(): ?Pret
    {
        return $this->pret;
    }

    public function setPret(?Pret $pret): static
    {
        $this->pret = $pret;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }
}
