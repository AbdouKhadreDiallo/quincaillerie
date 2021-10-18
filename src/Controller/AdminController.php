<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AdminController extends AbstractController
{
    private $serializer;
    private $validator;
    private $manager;
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage,   SerializerInterface $serializer,EntityManagerInterface $manager, ValidatorInterface $validator){
        // $this->userService = $userService;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @Route("/api/admins/connected", name="connected", methods={"GET"})
     */
    public function connected()
    {
        $connected = $this->tokenStorage->getToken()->getUser();
        return $this->json($connected, Response::HTTP_OK);
    }
    /**
     * @Route("/api/admins/magasins", name="adminMagasin", methods={"GET"})
    */
    public function magasins()
    {
        $connected = $this->tokenStorage->getToken()->getUser();
        return $this->json($connected->getMagasin(), Response::HTTP_OK);
    }
     /**
     * @Route("/api/admins/magasins/clients", name="clients", methods={"GET"})
    */
    public function clients()
    {
        $connected = $this->tokenStorage->getToken()->getUser();
        return $this->json($connected->getMagasin()->getClients(), Response::HTTP_OK);
    }
     /**
     * @Route("/api/admins/magasins/produits", name="produits", methods={"GET"})
    */
    public function produits()
    {
        $connected = $this->tokenStorage->getToken()->getUser();
        return $this->json($connected->getMagasin()->getProduits(), Response::HTTP_OK);
    }
}
