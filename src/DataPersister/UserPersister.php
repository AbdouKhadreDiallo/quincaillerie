<?php

namespace App\DataPersister;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class UserPersister implements ContextAwareDataPersisterInterface{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    public function persist($data, array $context = [])
    {
    }

    public function remove($data, array $context = [])
    {
        $data->getIsBlocked()==false?$data->setIsBlocked(true):$data->setIsBlocked(false);
        $this->entityManager->flush();
    }

}