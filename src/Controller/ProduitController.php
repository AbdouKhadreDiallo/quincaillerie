<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProduitController extends AbstractController
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
     * @Route("/api/produits", name="produit")
     */
    public function addProduit(Request $request)
    {
        if (!($this->tokenStorage->getToken()->getUser() instanceof Admin)) {
            return new JsonResponse("non authorisé",Response::HTTP_UNAUTHORIZED,[]);
        }
        // $produitTab = $this->serializer->decode($request->getContent(), 'json');
        $prixUnitaire = (int)$request->request->all()["prixUnitaire"];
        $quantite = (int)$request->request->all()["quantite"];
        // $request->request->all()["prixUnitaire"] = (int)$prixUnitaire;
        $produit = new Produit();
        $produit->setName($request->request->all()['name']);
        $produit->setPrixUnitaire($prixUnitaire);
        $produit->setQuantite($quantite);
        $produit->setDescription($request->request->all()["description"]);
        // $produit = $this->serializer->denormalize($request->request->all(),"App\Entity\Produit");
        $image = $request->files->get("image");
        if (!is_null($request->files->get("image"))) {
            $produit->setImage($this->uploadfile($request->files->get("image"), $produit->getName()));
        }
        $produit->setAddedBy($this->tokenStorage->getToken()->getUser());
        $produit->setMagasin($this->tokenStorage->getToken()->getUser()->getMagasin());
        $this->manager->persist($produit);
        $this->manager->flush();
        return $this->json($produit, Response::HTTP_CREATED);
        
    }
    public function uploadfile($file, $name)
    {
        $filetype = explode("/", $file->getMimeType())[1];
        $filePath = $file->getRealPath();
        return \file_get_contents($filePath, $name.'.'.$filetype);
    }
}
