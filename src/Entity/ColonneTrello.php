<?php

namespace App\Entity;

use App\Repository\ColonneTrelloRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ColonneTrelloRepository::class)]
class ColonneTrello
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column]
    private ?int $position = null;

    /**
     * @var Collection<int, CarteTrello>
     */
    #[ORM\OneToMany(targetEntity: CarteTrello::class, mappedBy: 'colonne', orphanRemoval: true)]
    private Collection $colonne;

    public function __construct()
    {
        $this->colonne = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection<int, CarteTrello>
     */
    public function getColonne(): Collection
    {
        return $this->colonne;
    }

    public function addColonne(CarteTrello $colonne): static
    {
        if (!$this->colonne->contains($colonne)) {
            $this->colonne->add($colonne);
            $colonne->setColonne($this);
        }

        return $this;
    }

    public function removeColonne(CarteTrello $colonne): static
    {
        if ($this->colonne->removeElement($colonne)) {
            // set the owning side to null (unless already changed)
            if ($colonne->getColonne() === $this) {
                $colonne->setColonne(null);
            }
        }

        return $this;
    }
}
