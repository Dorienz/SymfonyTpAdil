<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Profil;
use App\Entity\Customer;
use App\Entity\Invoice;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    function __construct(UserPasswordHasherInterface $passwordHasher )
    {
        
        $this->userPasswordHasherInterface = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        $profils = ['ADMIN', 'COMMERCIAL'];
        foreach ($profils as $key => $libelle) {
            $profil = new Profil();
            $profil->setLibelle($libelle);
            $manager->persist($profil);
            $manager->flush();

            for ($i=0; $i <=5 ; $i++) { 
                $user = new User();
                $user->setProfil($profil)
                     ->setLogin(strtolower($libelle).$i)
                     ->setFirstName($faker->firstName())
                     ->setlastName($faker->name());
            $hasher = $this->userPasswordHasherInterface->hashPassword($user, '1234');
            $user->setPassword($hasher);
            
            $manager->persist($user);
            $manager->flush();
            
            for($y=0;$y<=4;$y++)
            {
                $customer = new Customer();
                $customer->setUser($user)
                        ->setCompany($faker->company())
                        ->setEmail($faker->email())
                        ->setFirstName($faker->firstName())
                        ->setLastName($faker->lastName());
                
                $manager->persist($customer);
                $manager->flush($customer);

                for($x=0;$x<=2;$x++)
                {
                    $invoice = new Invoice();
                    $invoice->setCustomer($customer)
                            ->setAmount($faker->randomFloat(2))
                            ->setStatus($faker->randomElement(['SENT','PAID','CANCELLED']))
                            ->setChrono($faker->randomNumber(8,true))
                            ->setSentAt($faker->dateTimeBetween('-6 months')) ;
                    $manager->persist($invoice);
                    $manager->flush($invoice);
                 }
            }

            }
            
        }
    }
}
