<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserAddress;
use App\Entity\UserProfile;
use App\Model\UserInterface;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $encoder, UserRepository $userRepository)
    {
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 2; ++$i) {
            $user = new User();
            $user->setEmail('admin'.$i.'@example.com');
            $user->setUsername('admin'.$i);

            $password = $this->encoder->encodePassword($user, 'admin123');
            $user->setPassword($password);
            $user->setRoles([$user::ROLE_ADMIN]);

            $userProfile = new UserProfile();
            $userProfile->setFirstName('admin'.$i);
            $userProfile->setLastName('admin'.$i);
            $userProfile->setPhone('admin'.$i);
            $userProfile->setUserId($user);

            $userAddress = new UserAddress();
            $userAddress->setAddress('address_1');
            $userAddress->setUser($user);

            $userAddress2 = new UserAddress();
            $userAddress2->setAddress('address_2'.$i);
            $userAddress2->setUser($user);


            $manager->persist($user);
            $manager->persist($userProfile);
            $manager->persist($userAddress);
            $manager->persist($userAddress2);
            $manager->flush();
            $manager->clear();
        }
        for ($i = 1; $i <= 10; ++$i) {
            $user = new User();
            $user->setEmail('user'.$i.'@example.com');
            $user->setUsername('user'.$i);

            $password = $this->encoder->encodePassword($user, 'user123');
            $user->setPassword($password);
            $user->setRoles([$user::ROLE_USER]);

            $userProfile = new UserProfile();
            $userProfile->setFirstName('first_'.$i);
            $userProfile->setLastName('last'.$i);
            $userProfile->setPhone(rand(8231231231, 9231231231));
            $userProfile->setUserId($user);

            $userAddress = new UserAddress();
            $userAddress->setAddress('address_1');
            $userAddress->setUser($user);

            $userAddress2 = new UserAddress();
            $userAddress2->setAddress('address_2'.$i);
            $userAddress2->setUser($user);

            $manager->persist($user);
            $manager->persist($userProfile);
            $manager->persist($userAddress);
            $manager->persist($userAddress2);
            $manager->flush();
            $manager->clear();
        }
        echo "\n";
    }
}
