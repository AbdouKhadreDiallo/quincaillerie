<?php
namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

    
class UserService{
    private $encoder;
    private $serializer;
    private $repository;
    private $validator;
    private $manager;

    public function __construct(UserPasswordHasherInterface $encoder, SerializerInterface $serializer, UserRepository $repository, EntityManagerInterface $manager, ValidatorInterface $validator){
        $this->encoder = $encoder;
        $this->serializer = $serializer;
        $this->repository = $repository;
        $this->validator = $validator;
        $this->manager = $manager;
    }
    
    public function add($entite, $request)
    {
        $user = $request->request->all();
        $avatar = $request->files->get("avatar");        
        //on ouvre le fichier et on le lit en format binaire
        $avatar = fopen($avatar->getRealPath(), 'rb');
        $user["avatar"]=$avatar;
        $userExist = $this->repository->findBy(["username"=>$user["username"]]);
        // si l'utilisateur saisit est déjà dans notre bdd
        if (count($user)) {
            throw $this->createNotFoundException("Utilisateur déjà existant");
        }
        $user = $this->serializer->denormalize($user, $entite, true);
        $errors = $this->validator->validate($user);
        if(count($errors) > 0){
            $errors = $this->serializer->serialize($errors,'json');
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
        $user->setRoles($user->getRoles());
        $user->setPassword($this->encoder->hashPassword($user,"password"));
        $this->manager->persist($user);
        $this->manager->flush();
        fclose($avatar);
        return new JsonResponse("Créé avec success",Response::HTTP_CREATED,[],true);
    }
}