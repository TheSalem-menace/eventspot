<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_REF = 'user-admin';
    public const ORGA_REF = 'user-orga-';
    public const USER_REF = 'user-';

    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // 1 admin
        $admin = new User();
        $admin->setEmail('admin@eventspot.fr');
        $admin->setPseudo('Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);
        $this->addReference(self::ADMIN_REF, $admin);

        // 2 organisateurs
        for ($i = 0; $i < 2; $i++) {
            $orga = new User();
            $orga->setEmail("organisateur{$i}@eventspot.fr");
            $orga->setPseudo($faker->userName());
            $orga->setRoles(['ROLE_ORGANISATEUR']);
            $orga->setPassword($this->hasher->hashPassword($orga, 'orga123'));
            $manager->persist($orga);
            $this->addReference(self::ORGA_REF . $i, $orga);
        }

        // 5 users normaux
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email());
            $user->setPseudo($faker->userName());
            $user->setRoles([]);
            $user->setPassword($this->hasher->hashPassword($user, 'user123'));
            $manager->persist($user);
            $this->addReference(self::USER_REF . $i, $user);
        }

        $manager->flush();
    }
}
