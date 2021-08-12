<?php

namespace App\Entity;

use App\Entity\User;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *      collectionOperations={
 *            "GET", "POST" = {
 *                  "security" = "is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERUSER')",
 *             },
 *      },
 *      itemOperations = {
 *          "GET", "PUT", "DELETE" = {
 *              "security" = "is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERUSER')",
 *          }
 *      }
 * )
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 */
class Admin extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity=Magasin::class, mappedBy="owner", cascade={"persist", "remove"})
     */
    private $magasin;

    /**
     * @ORM\OneToMany(targetEntity=Produit::class, mappedBy="addedBy")
     */
    private $produits;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="author")
     */
    private $depots;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
        $this->depots = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMagasin(): ?Magasin
    {
        return $this->magasin;
    }

    public function setMagasin(?Magasin $magasin): self
    {
        // unset the owning side of the relation if necessary
        if ($magasin === null && $this->magasin !== null) {
            $this->magasin->setOwner(null);
        }

        // set the owning side of the relation if necessary
        if ($magasin !== null && $magasin->getOwner() !== $this) {
            $magasin->setOwner($this);
        }

        $this->magasin = $magasin;

        return $this;
    }

    /**
     * @return Collection|Produit[]
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setAddedBy($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getAddedBy() === $this) {
                $produit->setAddedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setAuthor($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getAuthor() === $this) {
                $depot->setAuthor(null);
            }
        }

        return $this;
    }
}
