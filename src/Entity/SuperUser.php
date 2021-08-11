<?php

namespace App\Entity;

use App\Entity\User;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SuperUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=SuperUserRepository::class)
 */
class SuperUser extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
