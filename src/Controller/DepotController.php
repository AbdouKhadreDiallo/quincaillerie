<?php

namespace App\Controller;

use App\Entity\Admin;
use DateTimeImmutable;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DepotController extends AbstractController
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
     * @Route("/api/depots", name="depot")
     */
    public function deposer(Request $request, ClientRepository $clientRepository)
    {
        if (!($this->tokenStorage->getToken()->getUser() instanceof Admin)) {
            return new JsonResponse("nopp",Response::HTTP_UNAUTHORIZED,[]);
        }
        $depotJson = $request->getContent();
        $depotTableau = $this->serializer->decode($depotJson, 'json');
        $clientid = isset($depotTableau['client']['id'])? (int)$depotTableau["client"]["id"]:null;
        unset($depotTableau["client"]);
        $depot = $this->serializer->denormalize($depotTableau,"App\Entity\Depot");
        $depot->setDoneAt(new \DateTimeImmutable() );
        $client = $clientRepository->findOneBy(['id'=>$clientid]);
        $depot->setClient($client);
        $depot->setAuthor($this->tokenStorage->getToken()->getUser());
        $client->getCompte()->setMontant($client->getCompte()->getMontant() + $depot->getSomme());

        $this->manager->persist($depot);
        $this->manager->flush();
        return new JsonResponse("done with sucess",Response::HTTP_OK,[]);

        
       
    }
}
