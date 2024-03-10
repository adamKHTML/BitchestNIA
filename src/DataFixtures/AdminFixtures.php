<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class AdminFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $adminsData = [
            [
                'pseudo' => 'Advar',
                'email' => 'Argentikk@gmail.com',
                'password' => '$2y$10$TKcROZilILCUIDtuUiGmO.tPQUJYfudOi56N4hP14VoSAXujtB27O',
                'firstName' => 'Adam',
                'lastName' => 'Konate',
                'status' => 'active',
                'roles' => 'ROLE_ADMIN',
                'isVerified' => true,
            ],
            [
                'pseudo' => 'TheHelperX',
                'email' => 'buraciurii@gmail.com',
                'password' => '$2y$10$i/e5k2X40U9FOkAS5l4ZhuGExEpBeHmQoA1r7pzrJvh6ssUjVQD1O',
                'firstName' => 'Iuri',
                'lastName' => 'Buriac',
                'status' => 'active',
                'roles' => 'ROLE_ADMIN', 
                'isVerified' => true,
            ],
            [
                'pseudo' => 'nelson_nh',
                'email' => 'ayathayler@yahoo.com',
                'password' => '$2y$10$jmQfzrcCJcA4aEotW7kC2OkgS9Tavrh0S4mDmsxOpU0xtxJP1x9Xe',
                'firstName' => 'Nelson',
                'lastName' => 'Ayadokoun',
                'status' => 'active',
                'roles' => 'ROLE_ADMIN', 
                'isVerified' => true,
            ],
            [
                'pseudo' => 'AdminUser',
                'email' => 'Random@gmail.com',
                'password' => '$2y$10$gyVGI3uuSJHrBWbsZKm0H.0uzif46w87FOWYmq7zduqZy4Wp14bo.',
                'firstName' => 'Mr',
                'lastName' => 'X',
                'status' => 'active',
                'roles' => 'ROLE_ADMIN', 
                'isVerified' => true,
            ],
        ];
        foreach ($adminsData as $adminInfo) {
            $admin = new User();
            $admin
                ->setPseudo($adminInfo['pseudo'])
                ->setEmail($adminInfo['email'])
                ->setPassword($adminInfo['password'])
                ->setFirstName($adminInfo['firstName'])
                ->setLastName($adminInfo['lastName'])
                ->setStatus($adminInfo['status'])
                ->setRoles($adminInfo['roles'])
                ->setIsVerified($adminInfo['isVerified']);

            $manager->persist($admin);
        }

        $manager->flush();
    }
}
