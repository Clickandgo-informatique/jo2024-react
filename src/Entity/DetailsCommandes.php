<?php

namespace App\Entity;

use App\Repository\DetailsCommandesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsCommandesRepository::class)]
class DetailsCommandes
{
    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column]
    private ?int $prix = 0;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'detailsCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commandes $commande = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'detailsCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Offres $offres = null;

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getCommande(): ?Commandes
    {
        return $this->commande;
    }

    public function setCommande(?Commandes $commande): static
    {
        $this->commande = $commande;

        return $this;
    }

    public function getOffres(): ?Offres
    {
        return $this->offres;
    }

    public function setOffres(?Offres $offres): static
    {
        $this->offres = $offres;

        return $this;
    }
}
