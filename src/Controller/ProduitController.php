<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
     * @Route("/api/produits", methods={"POST"})
     */
    public function addProduit(Request $request)
    {
        if (!($this->tokenStorage->getToken()->getUser() instanceof Admin)) {
            return new JsonResponse("non authorisé",Response::HTTP_UNAUTHORIZED,[]);
        }
        // $produitTab = $this->serializer->decode($request->getContent(), 'json');
        if ($request->request->all()) {
            $prixUnitaire = (int)$request->request->all()["prixUnitaire"];
            $quantite = (int)$request->request->all()["quantite"];
            // $request->request->all()["prixUnitaire"] = (int)$prixUnitaire;
            $produit = new Produit();
            $produit->setName($request->request->all()['name']);
            $produit->setPrixUnitaire($prixUnitaire);
            $produit->setQuantite($quantite);
            $produit->setDescription($request->request->all()["description"]);
            $image = $request->files->get("image");
            if (!is_null($request->files->get("image"))) {
                // dd("yes");
                $produit->setImage($this->uploadfile($request->files->get("image"), $produit->getName()));
            }
            $produit->setAddedBy($this->tokenStorage->getToken()->getUser());
            $produit->setMagasin($this->tokenStorage->getToken()->getUser()->getMagasin());
            $this->manager->persist($produit);
        }
        
        // $produit = $this->serializer->denormalize($request->request->all(),"App\Entity\Produit");

        if (!is_null($request->files->get("produits"))) {
            $produits = $request->files->get('produits'); 
            $fileFolder = __DIR__ . '/../../public/uploads/'; 
            $filePathName = md5(uniqid()) . $produits->getClientOriginalName();
        
            try {
                $produits->move($fileFolder, $filePathName);
            } catch (FileException $e) {
                dd($e);
            } 

            //on lit le contenu du fichier excel
            $spreadsheet = IOFactory::load($fileFolder . $filePathName); 
            //on recupere le contenu du fichier sous forme de tableau
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            foreach ($sheetData as $value) {
                $product = new Produit();
                $product->setName($value['A']);
                $product->setPrixUnitaire($value['C']);
                $product->setQuantite($value['D']);
                $product->setDescription($value['B']);
                $product->setAddedBy($this->tokenStorage->getToken()->getUser());
                $product->setMagasin($this->tokenStorage->getToken()->getUser()->getMagasin());
                $this->manager->persist($product);
            } 
        }
        $this->manager->flush();
        return $this->json($produit, Response::HTTP_CREATED);
        
    }

    /**
     * @Route("/api/produits/{id}", methods={"POST"})
     */
    public function updateProduit($id, Request $request, ProduitRepository $productRepository)
    {
        $data = $request->request->all();
        if (!$data && is_null($request->files->get('image'))) {
            return new JsonResponse("vide", Response::HTTP_FORBIDDEN,[], true);
        }
        $produit = $productRepository->find($id);
        if (!$produit) {
            return new JsonResponse("not found", Response::HTTP_NOT_FOUND,[], true);
        }
        foreach ($data as $key => $value) {
            if ($key!= "_method") {
                $FirstMajuscume = "set".ucfirst(strtolower($key));
                if (method_exists("App\Entity\Produit", $FirstMajuscume)) {
                    $produit->$FirstMajuscume($value);
                }
            }
        }
        if (!is_null($request->files->get('image'))) {
            $produit->setImage($this->uploadfile($request->files->get('image'), $produit->getName()));
        }
        $this->manager->flush();
        return $this->json($produit, Response::HTTP_OK);
        
    }

     /**
     * @Route("/api/produits/upload",name="xlsx", methods={"POST"})
     */
    public function upload(Request $request)
    {
        dd("hello");
        $produits = $request->files->get('produits'); 
        //l'emplacement ou on va enregistrer les fichier
        $fileFolder = __DIR__ . '/../../public/uploads/'; 
        $filePathName = md5(uniqid()) . $produits->getClientOriginalName();
      
        try {
            $produits->move($fileFolder, $filePathName);
        } catch (FileException $e) {
            dd($e);
        } 

        //on lit le contenu du fichier excel
        $spreadsheet = IOFactory::load($fileFolder . $filePathName); 
        //on recupere le contenu du fichier sous forme de tableau
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        dd($sheetData); 
    }


    public function uploadfile($file, $name)
    {
        $filetype = explode("/", $file->getMimeType())[1];
        $filePath = $file->getRealPath();
        return \file_get_contents($filePath, $name.'.'.$filetype);
    }
}
