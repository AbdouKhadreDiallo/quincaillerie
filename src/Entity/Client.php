<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *          collectionOperations={
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
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"magasin:read"})
     * @Groups({"admin:read"})
     */
    private $adress;

    /**
     * @ORM\OneToOne(targetEntity=Compte::class, mappedBy="proprio", cascade={"persist", "remove"})
     */
    private $compte;

    /**
     * @ORM\ManyToOne(targetEntity=Magasin::class, inversedBy="clients")
     */
    private $magasin;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="client")
     */
    private $depots;

    public function __construct()
    {
        $this->depots = new ArrayCollection();
    }

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(Compte $compte): self
    {
        // set the owning side of the relation if necessary
        if ($compte->getProprio() !== $this) {
            $compte->setProprio($this);
        }

        $this->compte = $compte;

        return $this;
    }

    public function getMagasin(): ?Magasin
    {
        return $this->magasin;
    }

    public function setMagasin(?Magasin $magasin): self
    {
        $this->magasin = $magasin;

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
            $depot->setClient($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getClient() === $this) {
                $depot->setClient(null);
            }
        }

        return $this;
    }

    
}
