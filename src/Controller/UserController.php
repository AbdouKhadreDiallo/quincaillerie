<?php

namespace App\Controller;

use App\Entity\Compte;
use DateTimeImmutable;
use App\Service\UserService;
use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{

    private $encoder;
    // private $userService;
    private $serializer;
    private $repository;
    private $validator;
    private $profilRepository;
    private $manager;

    public function __construct(UserPasswordHasherInterface $encoder,  SerializerInterface $serializer, UserRepository $repository, EntityManagerInterface $manager,ProfilRepository $profilRepository, ValidatorInterface $validator){
        $this->encoder = $encoder;
        // $this->userService = $userService;
        $this->serializer = $serializer;
        $this->repository = $repository;
        $this->validator = $validator;
        $this->manager = $manager;
        $this->profilRepository = $profilRepository;
    }
    /**
     * @Route("/api/admins", methods={"POST"})
     * 
     */
    public function addAdmins(Request $request)
    {
        return $this->add("App\Entity\Admin", $request);
    }
    /**
     * @Route("/api/clients", methods={"POST"})
     * 
     */
    public function addClients(Request $request)
    {
        return $this->add("App\Entity\Client", $request);
    }
    /**
     * @Route("/api/admins/{id}", methods={"POST"})
     * 
    */
    public function updateAdmin($id, Request $request){
        return $this->updateUser("App\Entity\Admin", $request, $id);
    }

    
    public function add($entite, $request)
    {
        $user = $request->request->all();
        $avatar = $request->files->get("avatar"); 
        
        if ($user["compte"]) {
            $withCompte = $user["compte"];
            unset($user["compte"]);
        }
        
        
        //on ouvre le fichier et on le lit en format binaire
        // $avatar = fopen($avatar->getRealPath(), 'rb');
        // $user["avatar"]=$avatar;
        $userExist = $this->repository->findBy(["username"=>$user["username"]]);
        // si l'utilisateur saisit est déjà dans notre bdd
        if (count($userExist)) {
            throw $this->createNotFoundException("Utilisateur déjà existant");
        }
        $user = $this->serializer->denormalize($user, $entite, true);
        $errors = $this->validator->validate($user);
        if(count($errors) > 0){
            $errors = $this->serializer->serialize($errors,'json');
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
        if (!is_null($avatar)) {
            $user->setAvatar($this->uploadfile($avatar, 'avatar'));
        }
        $user->setProfil($this->assign_Profil($entite));

        // if the user is a client 
        if ($this->assign_Profil($entite)->getLibelle() == "Client") {
            if (isset($withCompte) && $withCompte=="oui") {
                $date = new \DateTimeImmutable();
                $compte = new Compte();
                $compte->setProprio($user);
                $compte->setMontant(0);
                $compte->setCreatedAt($date);
                $compte->setNumeroCompte("AZER");
                $this->manager->persist($compte);
                $user->setCompte($compte);
            }
        }
        $user->setRoles($user->getRoles());
        $user->setPassword($this->encoder->hashPassword($user,"password"));
        $this->manager->persist($user);
        $this->manager->flush();
        // fclose($avatar);
        return new JsonResponse("done with sucess",Response::HTTP_CREATED,[]);
    }

    public function uploadfile($file, $name)
    {
        $filetype = explode("/", $file->getMimeType())[1];
        $filePath = $file->getRealPath();
        return \file_get_contents($filePath, $name.'.'.$filetype);
    }

    public function assign_Profil($entity)
    {
        $tab = ['Client', 'SuperUser', 'Admin', 'Diallo'];
        foreach ($tab as $value) {
            if (strstr($entity, $value)) {
                if ($value == "Client") {
                    return $this->profilRepository->findOneBy(["libelle" => "Client"]);
                }
                elseif ($value == "Admin") {
                    return $this->profilRepository->findOneBy(["libelle" => "Admin"]);
                }
                elseif ($value == "SuperUser") {
                    return $this->profilRepository->findOneBy(["libelle" => "SuperUser"]);
                }
                else {
                    return $this->profilRepository->findOneBy(["libelle" => "Diallo"]);
                }
            }
        }
    }

    public function updateUser($entity, $request, $id)
    {
        $data = $request->request->all();
        // dd($data);
        if (!$data) {
            return new JsonResponse("vide", Response::HTTP_FORBIDDEN,[], true);
        }
        $refEnCours = $this->repository->find($id);
        if (!$refEnCours) {
            return new JsonResponse("users not found", Response::HTTP_NOT_FOUND,[], true);
        }
        $avatar = $request->files->get('avatar');

        // dynamiser les if/else
        foreach ($data as $key => $value) {
            if ($key!= "_method") {
                $FirstMajuscume = "set".ucfirst(strtolower($key));
                if (method_exists($entity, $FirstMajuscume)) {
                    $refEnCours->$FirstMajuscume($value);
                }
            }
        }

        if (!is_null($avatar)){
            $refEnCours->setAvatar($this->uploadfile($avatar, 'avatar'));
        }
        
        $this->manager->flush();
        return new JsonResponse("Modifier avec succes", Response::HTTP_OK,[], true);
    }
}
