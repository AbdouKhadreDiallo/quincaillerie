<?php

namespace App\DataFixtures;

use App\Entity\Profil;
use App\Entity\SuperUser;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordHasherInterface $encoder){
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $profil = new Profil();
        $profil->setLibelle("SuperUser");
        $manager->persist($profil);

        $newSuperUser = new SuperUser();
        $newSuperUser->setUsername("khadr");
        $newSuperUser->setPrenom("Andou");
        $newSuperUser->setNom("Diallo");
        $newSuperUser->setPassword($this->encoder->hashPassword($newSuperUser, 'password'));
        $newSuperUser->setProfil($profil);
        $newSuperUser->setRoles($newSuperUser->getRoles());

        $manager->persist($newSuperUser);
        $manager->flush();
    }
}
