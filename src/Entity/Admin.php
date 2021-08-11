<?php

namespace App\Entity;

use App\Entity\User;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AdminRepository;
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

    public function getId(): ?int
    {
        return $this->id;
    }
}
