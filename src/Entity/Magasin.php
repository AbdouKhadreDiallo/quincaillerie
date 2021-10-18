<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MagasinRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *      normalizationContext={"groups":{"magasin:read"}},
 * )
 * @ORM\Entity(repositoryClass=MagasinRepository::class)
 */
class Magasin
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"magasin:read","admin:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"magasin:read","admin:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"magasin:read","admin:read"})
     */
    private $adress;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"magasin:read","admin:read"})
     */
    private $isBlocked = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"magasin:read","admin:read"})
     */
    private $telephone;

    /**
     * @ORM\OneToMany(targetEntity=Client::class, mappedBy="magasin")
     * @Groups({"magasin:read","admin:read"})
     */
    private $clients;

    /**
     * @ORM\OneToOne(targetEntity=Admin::class, inversedBy="magasin", cascade={"persist", "remove"})
     * @Groups({"magasin:read"})
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=SuperUser::class, inversedBy="magasins")
     * @Groups({"magasin:read"})
     */
    private $addedBy;

    /**
     * @ORM\OneToMany(targetEntity=Produit::class, mappedBy="magasin")
     * @Groups({"magasin:read","admin:read"})
     */
    private $produits;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $logo;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getIsBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(?bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->setMagasin($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getMagasin() === $this) {
                $client->setMagasin(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?Admin
    {
        return $this->owner;
    }

    public function setOwner(?Admin $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getAddedBy(): ?SuperUser
    {
        return $this->addedBy;
    }

    public function setAddedBy(?SuperUser $addedBy): self
    {
        $this->addedBy = $addedBy;

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
            $produit->setMagasin($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getMagasin() === $this) {
                $produit->setMagasin(null);
            }
        }

        return $this;
    }

    public function getLogo()
    {
        //return $this->logo;
        return $this->logo!=null?stream_get_contents($this->logo):null;
    }

    public function setLogo($logo): self
    {
        // $this->logo = $logo;
        $this->logo = \base64_encode($logo);

        return $this;
    }
}
