<?php

namespace App\Entity;

use App\Entity\User;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

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
     */
    private $adress;

    /**
     * @ORM\OneToOne(targetEntity=Compte::class, mappedBy="proprio", cascade={"persist", "remove"})
     */
    private $compte;

   

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

    
}
