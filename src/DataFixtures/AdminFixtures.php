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
                'password' => '24e3494df70d017727e102a86407594a1c799d3fce507c254970e2bc4281478b',
                'firstName' => 'Iuri',
                'lastName' => 'Buriac',
                'status' => 'active',
                'roles' => 'ROLE_ADMIN', 
                'isVerified' => true,
            ],
            [
                'pseudo' => 'nelson_nh',
                'email' => 'ayathayler@yahoo.com',
                'password' => 'fb3d661139dca2b8f4500998d9c248a30233d529d9a0f0155c1db00dd9883532',
                'firstName' => 'Nelson',
                'lastName' => 'Ayadokoun',
                'status' => 'active',
                'roles' => 'ROLE_ADMIN', 
                'isVerified' => true,
            ],
            [
                'pseudo' => 'AdminUser',
                'email' => 'Random@gmail.com',
                'password' => 'b603f8fc2ea8173a282c83be7071982b8b4bc2afde32c3d4f2e92cd2fb24b778',
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
