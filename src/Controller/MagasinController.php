<?php

namespace App\Controller;

use App\Repository\MagasinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MagasinController extends AbstractController
{

    private $serializer;
    private $validator;
    private $manager;
    private $tokenStorage;
    private $magasinRepository;
    public function __construct(MagasinRepository $magasinRepository, TokenStorageInterface $tokenStorage,   SerializerInterface $serializer,EntityManagerInterface $manager, ValidatorInterface $validator){
        // $this->userService = $userService;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
        $this->magasinRepository = $magasinRepository;
    }

    /**
     * @Route("/api/magasins/{id}", name="magasin", methods={"POST"})
     */
    public function updateMagasin($id, Request $request)
    {
        $data = $request->request->all();
        // dd($data);
        if (!$data && is_null($request->files->get('logo'))) {
            return new JsonResponse("vide", Response::HTTP_FORBIDDEN,[], true);
        }
        $magasin = $this->magasinRepository->find($id);
        if (!$magasin) {
            return new JsonResponse("magasin with this id doesn't exist", Response::HTTP_NOT_FOUND,[], true);
        }
        if ($magasin->getOwner() != $this->tokenStorage->getToken()->getUser()) {
            return new JsonResponse("you're not allowed to do this", Response::HTTP_FORBIDDEN,[], true);
        }
        if (!is_null($request->files->get('logo'))) {
            $magasin->setLogo($this->uploadfile($request->files->get('logo'), "logo"));
        }
        foreach ($data as $key => $value) {
            if ($key!= "_method") {
                $FirstMajuscume = "set".ucfirst(strtolower($key));
                if (method_exists("App\Entity\Magasin", $FirstMajuscume)) {
                    $magasin->$FirstMajuscume($value);
                }
            }
        }
        $this->manager->flush();
        return $this->json($magasin, Response::HTTP_OK);

    }
    public function uploadfile($file, $name)
    {
        $filetype = explode("/", $file->getMimeType())[1];
        $filePath = $file->getRealPath();
        return \file_get_contents($filePath, $name.'.'.$filetype);
    }
}
