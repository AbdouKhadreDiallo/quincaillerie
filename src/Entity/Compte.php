<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompteRepository::class)
 */
class Compte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer", length=255)
     */
    private $montant;

    /**
     * @ORM\OneToOne(targetEntity=Client::class, inversedBy="compte", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $proprio;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero_compte;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBlocked = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getProprio(): ?Client
    {
        return $this->proprio;
    }

    public function setProprio(Client $proprio): self
    {
        $this->proprio = $proprio;

        return $this;
    }

    public function getNumeroCompte(): ?string
    {
        return $this->numero_compte;
    }

    public function setNumeroCompte(string $numero_compte): self
    {
        $this->numero_compte = $numero_compte;

        return $this;
    }

    public function getIsBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }
}
